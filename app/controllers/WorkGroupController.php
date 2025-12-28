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
    Helpers::view('work_groups/index', ['title'=>'Gruppi','groups'=>$groups]);
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
    $students = (new Student())->all();
    Helpers::view('work_groups/form', ['title'=>'Nuovo Gruppo','group'=>null,'students'=>$students]);
  }
  public function store() {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $id = (new WorkGroup())->create(['name'=>trim($_POST['name'] ?? ''),'leader_student_id'=>$_POST['leader_student_id']]);
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
    Helpers::redirect('/work-groups/'.$id);
  }
  public function addMember($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new WorkGroup())->addMember($id,$_POST['student_id'],$_POST['role'] ?? 'installer');
    Helpers::redirect('/work-groups/'.$id);
  }
  public function removeMember($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new WorkGroup())->removeMember($id,$_POST['student_id']);
    Helpers::redirect('/work-groups/'.$id);
  }
}

