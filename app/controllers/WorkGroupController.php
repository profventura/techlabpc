<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\WorkGroup;
use App\Models\Student;
use App\Models\Laptop;
class WorkGroupController {
  public function index() {
    Auth::require();
    $groups = (new WorkGroup())->all();
    $pdo = \App\Core\DB::conn();
    $groups_total = count($groups);
    $students_total = (int)$pdo->query('SELECT COUNT(*) c FROM group_members')->fetch()['c'];
    $laptops_total = (int)$pdo->query('SELECT COUNT(*) c FROM laptops WHERE group_id IS NOT NULL')->fetch()['c'];
    $summary = ['groups'=>$groups_total,'students'=>$students_total,'laptops'=>$laptops_total];
    Helpers::view('work_groups/index', ['title'=>'Gruppi','groups'=>$groups,'summary'=>$summary]);
  }
  public function show($id) {
    Auth::require();
    $wg = new WorkGroup();
    $group = $wg->find($id);
    $members = $wg->members($id);
    $laptops = $wg->laptops($id);
    Helpers::view('work_groups/show', ['title'=>'Gruppo','group'=>$group,'members'=>$members,'laptops'=>$laptops]);
  }
  public function createForm() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $students = (new Student())->withoutGroup();
    Helpers::view('work_groups/form', ['title'=>'Nuovo Gruppo','group'=>null,'students'=>$students]);
  }
  public function store() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $id = (new WorkGroup())->create(['name'=>trim($_POST['name'] ?? ''),'leader_student_id'=>$_POST['leader_student_id']]);
    (new WorkGroup())->setLeader($id, $_POST['leader_student_id']);
    (new \App\Models\Log())->addAction('create_group', \App\Core\Auth::user()['id'] ?? null, ['group_id'=>$id, 'note'=>trim($_POST['name'] ?? '')]);
    Helpers::redirect('/work-groups/'.$id);
  }
  public function editForm($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $group = (new WorkGroup())->find($id);
    $students = (new Student())->all();
    Helpers::view('work_groups/form', ['title'=>'Modifica Gruppo','group'=>$group,'students'=>$students]);
  }
  public function update($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new WorkGroup())->update($id,['name'=>trim($_POST['name'] ?? ''),'leader_student_id'=>$_POST['leader_student_id']]);
    (new WorkGroup())->setLeader($id, $_POST['leader_student_id']);
    (new \App\Models\Log())->addAction('update_group', \App\Core\Auth::user()['id'] ?? null, ['group_id'=>$id, 'note'=>trim($_POST['name'] ?? '')]);
    Helpers::redirect('/work-groups/'.$id);
  }
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
    Helpers::redirect('/work-groups/'.$id);
  }
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
    Helpers::redirect('/work-groups/'.$id);
  }
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
    Helpers::redirect('/work-groups');
  }
}

