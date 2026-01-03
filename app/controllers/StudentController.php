<?php
/*
  File: StudentController.php
  Scopo: Gestisce le operazioni sugli Studenti (lista, creazione, modifica, eliminazione, import/export).
  Spiegazione: Controlla i permessi admin, coordina filtri di ricerca e aggiorna le card riepilogative.
*/
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Student;
class StudentController {
  // Lista studenti con filtri e card (solo admin)
  public function index() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $filters = [
      'role' => $_GET['role'] ?? null,
      'active' => isset($_GET['active']) ? ($_GET['active'] === '' ? '' : (int)$_GET['active']) : '',
      'group_id' => $_GET['group_id'] ?? null,
      'q' => trim($_GET['q'] ?? '')
    ];
    $students = (new Student())->all($filters);
    $groups = (new \App\Models\WorkGroup())->all();
    $pdo = \App\Core\DB::conn();
    $stmt = $pdo->prepare('SELECT metric, value FROM view_cards WHERE scope=?');
    $stmt->execute(['students']);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      \App\Services\ViewCardService::refreshStudents();
      $stmt->execute(['students']);
      $rows = $stmt->fetchAll();
    }
    $m = [];
    foreach ($rows as $r) { $m[$r['metric']] = (int)$r['value']; }
    $summary = ['students'=>$m['students'] ?? 0,'leaders'=>$m['leaders'] ?? 0,'installers'=>$m['installers'] ?? 0];
    Helpers::view('students/index', ['title'=>'Studenti','students'=>$students,'filters'=>$filters,'groups'=>$groups,'summary'=>$summary]);
  }
  // Visualizza form di creazione nuovo studente
  public function createForm() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    Helpers::view('students/form', ['title'=>'Nuovo Studente','student'=>null]);
  }
  // Salva nuovo studente, con password obbligatoria e aggiornamento card
  public function store() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = [
      'first_name'=>trim($_POST['first_name'] ?? ''),
      'last_name'=>trim($_POST['last_name'] ?? ''),
      'email'=>trim($_POST['email'] ?? ''),
      'password'=>$_POST['password'] ?? '',
      'role'=>$_POST['role'] ?? 'student',
      'active'=>isset($_POST['active']) ? 1 : 0
    ];
    if (empty($data['password'])) {
      Helpers::view('students/form', ['title'=>'Nuovo Studente','student'=>$data, 'error'=>'La password è obbligatoria']);
      return;
    }
    (new Student())->create($data);
    (new \App\Models\Log())->addAction('create_student', \App\Core\Auth::user()['id'] ?? null, ['note'=>$data['email']]);
    \App\Services\ViewCardService::refreshStudents();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/students');
  }
  // Visualizza form di modifica studente
  public function editForm($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $s = (new Student())->find($id);
    Helpers::view('students/form', ['title'=>'Modifica Studente','student'=>$s]);
  }
  // Aggiorna dati studente e registra log
  public function update($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = [
      'first_name'=>trim($_POST['first_name'] ?? ''),
      'last_name'=>trim($_POST['last_name'] ?? ''),
      'email'=>trim($_POST['email'] ?? ''),
      'password'=>$_POST['password'] ?? '',
      'role'=>$_POST['role'] ?? 'student',
      'active'=>isset($_POST['active']) ? 1 : 0
    ];
    (new Student())->update($id,$data);
    (new \App\Models\Log())->addAction('update_student', \App\Core\Auth::user()['id'] ?? null, ['note'=>$data['email']]);
    \App\Services\ViewCardService::refreshStudents();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/students');
  }
  // Elimina studente, registra log e aggiorna card
  public function delete($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Student())->delete($id);
    (new \App\Models\Log())->addAction('delete_student', \App\Core\Auth::user()['id'] ?? null, ['note'=>strval($id)]);
    \App\Services\ViewCardService::refreshStudents();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/students');
  }

  // Esporta studenti in CSV
  public function export() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $pdo = \App\Core\DB::conn();
    $students = $pdo->query('SELECT * FROM students ORDER BY last_name, first_name')->fetchAll();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=students_export_' . date('Y-m-d_H-i-s') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, ['id','first_name','last_name','email','password_hash','role','active','created_at','updated_at']);
    
    foreach ($students as $s) {
      fputcsv($output, [
        $s['id'],
        $s['first_name'],
        $s['last_name'],
        $s['email'],
        $s['password_hash'],
        $s['role'],
        $s['active'],
        $s['created_at'] ?? '',
        $s['updated_at'] ?? ''
      ]);
    }
    
    fclose($output);
    exit;
  }

  // Importa studenti da CSV (imposta password default se assente) e aggiorna card
  public function import() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
      $tmpName = $_FILES['csv_file']['tmp_name'];
      $handle = fopen($tmpName, 'r');
      
      if ($handle !== FALSE) {
        $header = fgetcsv($handle, 2000, ',');
        $model = new Student();
        $created = 0;
        $errors = [];
        $rownum = 1;
        $cols = [];
        foreach ($header as $i=>$h) { $cols[strtolower(trim($h))] = $i; }
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
          $rownum++;
          $get = function($key, $def=null) use ($cols,$data){ $idx = $cols[$key] ?? null; return $idx!==null ? $data[$idx] : $def; };
          $studentData = [
            'first_name' => $get('first_name',''),
            'last_name' => $get('last_name',''),
            'email' => $get('email',''),
            'role' => $get('role','student'),
            'active' => (int)$get('active',1),
          ];
          if (($pwd = $get('password', null)) !== null) { $studentData['password'] = $pwd; }
          elseif (($ph = $get('password_hash', null)) !== null) { $studentData['password_hash'] = $ph; }
          else { $studentData['password'] = '12345678'; }
          try {
            $model->create($studentData);
            $created++;
          } catch (\Throwable $e) {
            $errors[] = 'Riga '.$rownum.': '.$e->getMessage();
          }
        }
        fclose($handle);
        (new \App\Models\Log())->addAction('create_student', \App\Core\Auth::user()['id'] ?? null, ['note'=>'import '.$created.' items']);
        \App\Core\Helpers::addFlash('success', 'Import Studenti completato: creati '.$created);
        if ($errors) { \App\Core\Helpers::addFlash('danger', 'Errori: '.implode(' | ', $errors)); }
      }
    }
    \App\Services\ViewCardService::refreshStudents();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/students');
  }
}
