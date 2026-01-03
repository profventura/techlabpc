<?php
/*
  File: WorkGroupController.php
  Scopo: Gestisce i Gruppi di lavoro (lista, dettagli, creazione/modifica, gestione membri, import/export).
  Spiegazione: Coordina il modello WorkGroup, i membri e i laptop associati, aggiornando le card riepilogative.
*/
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\WorkGroup;
use App\Models\Student;
use App\Models\Laptop;
class WorkGroupController {
  // Lista dei gruppi con riepilogo card
  public function index() {
    Auth::require();
    $groups = (new WorkGroup())->all();
    $pdo = \App\Core\DB::conn();
    $stmt = $pdo->prepare('SELECT metric, value FROM view_cards WHERE scope=?');
    $stmt->execute(['groups']);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      \App\Services\ViewCardService::refreshGroups();
      $stmt->execute(['groups']);
      $rows = $stmt->fetchAll();
    }
    $m = [];
    foreach ($rows as $r) { $m[$r['metric']] = (int)$r['value']; }
    $summary = ['groups'=>$m['groups'] ?? count($groups),'students'=>$m['students'] ?? 0,'laptops'=>$m['laptops'] ?? 0];
    Helpers::view('work_groups/index', ['title'=>'Gruppi','groups'=>$groups,'summary'=>$summary]);
  }
  // Mostra il dettaglio del gruppo con membri e laptop associati
  public function show($id) {
    Auth::require();
    $wg = new WorkGroup();
    $group = $wg->find($id);
    $members = $wg->members($id);
    $laptops = $wg->laptops($id);
    Helpers::view('work_groups/show', ['title'=>'Gruppo','group'=>$group,'members'=>$members,'laptops'=>$laptops]);
  }
  // Visualizza form di creazione nuovo gruppo (solo admin)
  public function createForm() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $students = (new Student())->withoutGroup();
    Helpers::view('work_groups/form', ['title'=>'Nuovo Gruppo','group'=>null,'students'=>$students]);
  }
  // Salva nuovo gruppo, imposta leader e aggiorna card
  public function store() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $id = (new WorkGroup())->create(['name'=>trim($_POST['name'] ?? ''),'leader_student_id'=>$_POST['leader_student_id']]);
    (new WorkGroup())->setLeader($id, $_POST['leader_student_id']);
    (new \App\Models\Log())->addAction('create_group', \App\Core\Auth::user()['id'] ?? null, ['group_id'=>$id, 'note'=>trim($_POST['name'] ?? '')]);
    \App\Services\ViewCardService::refreshGroups();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/work-groups/'.$id);
  }
  // Visualizza form di modifica gruppo
  public function editForm($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $group = (new WorkGroup())->find($id);
    $students = (new Student())->all();
    Helpers::view('work_groups/form', ['title'=>'Modifica Gruppo','group'=>$group,'students'=>$students]);
  }
  // Aggiorna gruppo, imposta leader e aggiorna card
  public function update($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new WorkGroup())->update($id,['name'=>trim($_POST['name'] ?? ''),'leader_student_id'=>$_POST['leader_student_id']]);
    (new WorkGroup())->setLeader($id, $_POST['leader_student_id']);
    (new \App\Models\Log())->addAction('update_group', \App\Core\Auth::user()['id'] ?? null, ['group_id'=>$id, 'note'=>trim($_POST['name'] ?? '')]);
    \App\Services\ViewCardService::refreshGroups();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/work-groups/'.$id);
  }
  // Aggiunge un membro al gruppo, con gestione ruolo leader/installer
  public function addMember($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $role = $_POST['role'] ?? 'installer';
    $wg = new WorkGroup();
    $existingGroup = $wg->memberGroupOf($_POST['student_id']);
    if ($existingGroup !== null && $existingGroup !== (int)$id) {
      \App\Core\Helpers::addFlash('danger', 'Lo studente appartiene già a un altro gruppo');
      \App\Core\Helpers::redirect('/work-groups/'.$id);
      return;
    }
    if ($role === 'leader') {
      $wg->setLeader($id, $_POST['student_id']);
      (new \App\Models\Log())->addAction('assign_member_to_group', \App\Core\Auth::user()['id'] ?? null, ['group_id'=>$id, 'note'=>'leader '.$_POST['student_id']]);
    } else {
      $wg->addMember($id, $_POST['student_id'], $role);
      (new \App\Models\Log())->addAction('assign_member_to_group', \App\Core\Auth::user()['id'] ?? null, ['group_id'=>$id, 'note'=>$role.' '.$_POST['student_id']]);
    }
    \App\Services\ViewCardService::refreshGroups();
    \App\Services\ViewCardService::refreshStudents();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/work-groups/'.$id);
  }
  // Rimuove un membro, garantendo la presenza di almeno un leader
  public function removeMember($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $wg = new WorkGroup();
    $sid = $_POST['student_id'];
    if ($wg->isLeader($id, $sid) && $wg->leaderCount($id) <= 1) {
      \App\Core\Helpers::addFlash('danger', 'Ogni gruppo deve avere un leader');
      \App\Core\Helpers::redirect('/work-groups/'.$id);
      return;
    }
    $wg->removeMember($id,$sid);
    (new \App\Models\Log())->addAction('remove_member_from_group', \App\Core\Auth::user()['id'] ?? null, ['group_id'=>$id, 'note'=>strval($sid)]);
    \App\Services\ViewCardService::refreshGroups();
    \App\Services\ViewCardService::refreshStudents();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/work-groups/'.$id);
  }
  // Esporta gruppi in CSV
  public function export() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    $pdo = \App\Core\DB::conn();
    $groups = $pdo->query('SELECT * FROM work_groups ORDER BY name')->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=work_groups_export_' . date('Y-m-d_H-i-s') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['id','name','leader_student_id','created_at','updated_at']);
    foreach ($groups as $g) {
      fputcsv($output, [$g['id'], $g['name'], $g['leader_student_id'], $g['created_at'] ?? '', $g['updated_at'] ?? '']);
    }
    fclose($output);
    (new \App\Models\Log())->addAction('update_group', \App\Core\Auth::user()['id'] ?? null, ['note'=>'export '.count($groups).' items']);
    exit;
  }
  // Importa gruppi da CSV e aggiorna card
  public function import() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
      $tmpName = $_FILES['csv_file']['tmp_name'];
      $handle = fopen($tmpName, 'r');
      if ($handle !== FALSE) {
        $header = fgetcsv($handle, 2000, ',');
        $created = 0;
        $errors = [];
        $rownum = 1;
        $wm = new WorkGroup();
        $cols = [];
        foreach ($header as $i=>$h) { $cols[strtolower(trim($h))] = $i; }
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
          $rownum++;
          $get = function($key, $def=null) use ($cols,$data){ $idx = $cols[$key] ?? null; return $idx!==null ? $data[$idx] : $def; };
          $name = trim($get('name',''));
          $leader_id = is_numeric($get('leader_student_id',null)) ? (int)$get('leader_student_id',null) : null;
          if ($name === '' || !$leader_id) { $errors[] = 'Riga '.$rownum.': dati non validi'; continue; }
          try {
            $id = $wm->create(['name'=>$name,'leader_student_id'=>$leader_id]);
            $wm->setLeader($id, $leader_id);
            $created++;
          } catch (\Throwable $e) {
            $errors[] = 'Riga '.$rownum.': '.$e->getMessage();
          }
        }
        fclose($handle);
        \App\Core\Helpers::addFlash('success', 'Import Gruppi completato: creati '.$created);
        if ($errors) { \App\Core\Helpers::addFlash('danger', 'Errori: '.implode(' | ', $errors)); }
        (new \App\Models\Log())->addAction('create_group', \App\Core\Auth::user()['id'] ?? null, ['note'=>'import '.$created.' items']);
      }
    }
    \App\Services\ViewCardService::refreshGroups();
    \App\Services\ViewCardService::refreshStudents();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/work-groups');
  }
}

