<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Student;
class StudentController {
  public function index() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $students = (new Student())->all();
    Helpers::view('students/index', ['title'=>'Studenti','students'=>$students]);
  }
  public function createForm() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    Helpers::view('students/form', ['title'=>'Nuovo Studente','student'=>null]);
  }
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
    Helpers::redirect('/students');
  }
  public function editForm($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    $s = (new Student())->find($id);
    Helpers::view('students/form', ['title'=>'Modifica Studente','student'=>$s]);
  }
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
    Helpers::redirect('/students');
  }
  public function delete($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Student())->delete($id);
    Helpers::redirect('/students');
  }
}

