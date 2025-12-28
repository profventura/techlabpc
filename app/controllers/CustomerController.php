<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Customer;
class CustomerController {
  public function index() {
    Auth::require();
    $customers = (new Customer())->all();
    Helpers::view('customers/index', ['title'=>'Docenti','customers'=>$customers]);
  }
  public function show($id) {
    Auth::require();
    $m = new Customer();
    $c = $m->find($id);
    $laptops = $m->laptops($id);
    $payments = $m->payments($id);
    $office_total = array_reduce($laptops, function($acc,$l){ return $acc + (!empty($l['office_license']) ? 2 : 0); }, 0);
    Helpers::view('customers/show', ['title'=>'Docente','customer'=>$c,'laptops'=>$laptops,'payments'=>$payments,'office_total'=>$office_total]);
  }
  public function createForm() {
    Auth::require();
    Helpers::view('customers/form', ['title'=>'Nuovo Docente','customer'=>null]);
  }
  public function store() {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = ['first_name'=>trim($_POST['first_name'] ?? ''),'last_name'=>trim($_POST['last_name'] ?? ''),'email'=>trim($_POST['email'] ?? ''),'notes'=>trim($_POST['notes'] ?? '')];
    $id = (new Customer())->create($data);
    Helpers::redirect('/customers/'.$id);
  }
  public function editForm($id) {
    Auth::require();
    $c = (new Customer())->find($id);
    Helpers::view('customers/form', ['title'=>'Modifica Docente','customer'=>$c]);
  }
  public function update($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = ['first_name'=>trim($_POST['first_name'] ?? ''),'last_name'=>trim($_POST['last_name'] ?? ''),'email'=>trim($_POST['email'] ?? ''),'notes'=>trim($_POST['notes'] ?? '')];
    (new Customer())->update($id,$data);
    Helpers::redirect('/customers/'.$id);
  }
  public function delete($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Customer())->delete($id);
    Helpers::redirect('/customers');
  }
}

