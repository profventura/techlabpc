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
    $pdo = \App\Core\DB::conn();
    $docenti = (int)$pdo->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];
    $pc_richiesti = (int)$pdo->query('SELECT COALESCE(SUM(pc_requested_count),0) s FROM customers')->fetch()['s'];
    $pc_assegnati = (int)$pdo->query('SELECT COUNT(*) c FROM laptops WHERE customer_id IS NOT NULL')->fetch()['c'];
    $pc_pagati = (int)$pdo->query("SELECT COALESCE(SUM(pcs_paid_count),0) s FROM payment_transfers WHERE status='verified'")->fetch()['s'];
    $summary = ['docenti'=>$docenti,'pc_richiesti'=>$pc_richiesti,'pc_assegnati'=>$pc_assegnati,'pc_pagati'=>$pc_pagati];
    Helpers::view('customers/index', ['title'=>'Docenti','customers'=>$customers,'summary'=>$summary]);
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
    $pdo = \App\Core\DB::conn();
    $customers = $pdo->query('SELECT * FROM customers ORDER BY last_name, first_name')->fetchAll();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=customers_export_' . date('Y-m-d_H-i-s') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, ['id','first_name','last_name','email','notes','pc_requested_count','created_at','updated_at']);
    
    foreach ($customers as $c) {
      fputcsv($output, [
        $c['id'],
        $c['first_name'],
        $c['last_name'],
        $c['email'],
        $c['notes'],
        (int)($c['pc_requested_count'] ?? 0),
        $c['created_at'] ?? '',
        $c['updated_at'] ?? ''
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
        $header = fgetcsv($handle, 2000, ',');
        $model = new Customer();
        $created = 0;
        $errors = [];
        $rownum = 1;
        $cols = [];
        foreach ($header as $i=>$h) { $cols[strtolower(trim($h))] = $i; }
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
          $rownum++;
          $get = function($key, $def=null) use ($cols,$data){ $idx = $cols[$key] ?? null; return $idx!==null ? $data[$idx] : $def; };
          $customerData = [
            'first_name' => $get('first_name',''),
            'last_name' => $get('last_name',''),
            'email' => $get('email',''),
            'notes' => $get('notes',''),
            'pc_requested_count' => is_numeric($get('pc_requested_count',0)) ? (int)$get('pc_requested_count',0) : 0
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
