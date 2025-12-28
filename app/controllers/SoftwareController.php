<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Software;
class SoftwareController {
  public function index() {
    Auth::require();
    $items = (new Software())->all();
    Helpers::view('software/index', ['title'=>'Software','items'=>$items]);
  }
  public function createForm() {
    Auth::require();
    Helpers::view('software/form', ['title'=>'Nuovo Software','item'=>null]);
  }
  public function store() {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = [
      'name'=>trim($_POST['name'] ?? ''),
      'version'=>trim($_POST['version'] ?? ''),
      'license'=>trim($_POST['license'] ?? ''),
      'notes'=>trim($_POST['notes'] ?? ''),
      'cost'=>trim($_POST['cost'] ?? ''),
    ];
    $id = (new Software())->create($data);
    Helpers::redirect('/software/'.$id.'/edit');
  }
  public function editForm($id) {
    Auth::require();
    $item = (new Software())->find($id);
    Helpers::view('software/form', ['title'=>'Modifica Software','item'=>$item]);
  }
  public function update($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = [
      'name'=>trim($_POST['name'] ?? ''),
      'version'=>trim($_POST['version'] ?? ''),
      'license'=>trim($_POST['license'] ?? ''),
      'notes'=>trim($_POST['notes'] ?? ''),
      'cost'=>trim($_POST['cost'] ?? ''),
    ];
    (new Software())->update($id, $data);
    Helpers::redirect('/software');
  }
  public function delete($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Software())->delete($id);
    Helpers::redirect('/software');
  }
}
