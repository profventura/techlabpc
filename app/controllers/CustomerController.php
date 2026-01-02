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
    Helpers::view('customers/show', ['title'=>'Docente','customer'=>$c,'laptops'=>$laptops,'payments'=>$payments]);
  }
  public function createForm() {
    Auth::require();
    Helpers::view('customers/form', ['title'=>'Nuovo Docente','customer'=>null]);
  }
  public function store() {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = ['first_name'=>trim($_POST['first_name'] ?? ''),'last_name'=>trim($_POST['last_name'] ?? ''),'email'=>trim($_POST['email'] ?? ''),'notes'=>trim($_POST['notes'] ?? ''),'pc_requested_count'=>intval($_POST['pc_requested_count'] ?? 0)];
    $id = (new Customer())->create($data);
    (new \App\Models\Log())->addAction('create_customer', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$id, 'note'=>$data['email']]);
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
    $data = ['first_name'=>trim($_POST['first_name'] ?? ''),'last_name'=>trim($_POST['last_name'] ?? ''),'email'=>trim($_POST['email'] ?? ''),'notes'=>trim($_POST['notes'] ?? ''),'pc_requested_count'=>intval($_POST['pc_requested_count'] ?? 0)];
    (new Customer())->update($id,$data);
    (new \App\Models\Log())->addAction('update_customer', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$id, 'note'=>$data['email']]);
    Helpers::redirect('/customers/'.$id);
  }
  public function delete($id) {
    Auth::require();
    if (!\App\Core\Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Customer())->delete($id);
    (new \App\Models\Log())->addAction('delete_customer', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$id]);
    Helpers::redirect('/customers');
  }

  public function export() {
    Auth::require();
    $customers = (new Customer())->all();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=customers_export_' . date('Y-m-d_H-i-s') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Header
    fputcsv($output, ['first_name', 'last_name', 'email', 'notes']);
    
    foreach ($customers as $c) {
      fputcsv($output, [
        $c['first_name'],
        $c['last_name'],
        $c['email'],
        $c['notes']
      ]);
    }
    
    fclose($output);
    exit;
  }

  public function import() {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
      $tmpName = $_FILES['csv_file']['tmp_name'];
      $handle = fopen($tmpName, 'r');
      
      if ($handle !== FALSE) {
        $header = fgetcsv($handle, 1000, ',');
        $model = new Customer();
        $created = 0;
        $errors = [];
        $rownum = 1;
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
          $rownum++;
          if (count($data) < 4) { $errors[] = 'Riga '.$rownum.': colonne insufficienti'; continue; }
          $customerData = [
            'first_name' => $data[0],
            'last_name' => $data[1],
            'email' => $data[2],
            'notes' => $data[3]
          ];
          try {
            $model->create($customerData);
            $created++;
          } catch (\Throwable $e) {
            $errors[] = 'Riga '.$rownum.': '.$e->getMessage();
          }
        }
        fclose($handle);
        \App\Core\Helpers::addFlash('success', 'Import Docenti completato: creati '.$created);
        if ($errors) { \App\Core\Helpers::addFlash('danger', 'Errori: '.implode(' | ', $errors)); }
        (new \App\Models\Log())->addAction('create_customer', \App\Core\Auth::user()['id'] ?? null, ['note'=>'import '.$created.' items']);
      }
    }
    Helpers::redirect('/customers');
  }
}
