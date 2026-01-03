<?php
/*
  File: CustomerController.php
  Scopo: Gestisce le operazioni sui Docenti (lista, dettaglio, creazione, modifica, eliminazione, import/export).
  Spiegazione: Coordina il modello Customer e le relative viste, aggiornando anche le card riepilogative.
*/
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Customer;
class CustomerController {
  // Lista dei docenti con riepilogo card
  public function index() {
    Auth::require();
    $customers = (new Customer())->all();
    $pdo = \App\Core\DB::conn();
    $stmt = $pdo->prepare('SELECT metric, value FROM view_cards WHERE scope=?');
    $stmt->execute(['customers']);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      \App\Services\ViewCardService::refreshCustomers();
      $stmt->execute(['customers']);
      $rows = $stmt->fetchAll();
    }
    $m = [];
    foreach ($rows as $r) { $m[$r['metric']] = (int)$r['value']; }
    $summary = ['docenti'=>$m['docenti'] ?? 0,'pc_richiesti'=>$m['pc_richiesti'] ?? 0,'pc_assegnati'=>$m['pc_assegnati'] ?? 0,'pc_pagati'=>$m['pc_pagati'] ?? 0];
    Helpers::view('customers/index', ['title'=>'Docenti','customers'=>$customers,'summary'=>$summary]);
  }
  // Mostra il dettaglio di un docente con i PC e i pagamenti associati
  public function show($id) {
    Auth::require();
    $m = new Customer();
    $c = $m->find($id);
    $laptops = $m->laptops($id);
    $payments = $m->payments($id);
    Helpers::view('customers/show', ['title'=>'Docente','customer'=>$c,'laptops'=>$laptops,'payments'=>$payments]);
  }
  // Visualizza form di creazione nuovo docente
  public function createForm() {
    Auth::require();
    Helpers::view('customers/form', ['title'=>'Nuovo Docente','customer'=>null]);
  }
  // Salva nuovo docente, registra log e aggiorna le card
  public function store() {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = ['first_name'=>trim($_POST['first_name'] ?? ''),'last_name'=>trim($_POST['last_name'] ?? ''),'email'=>trim($_POST['email'] ?? ''),'notes'=>trim($_POST['notes'] ?? ''),'pc_requested_count'=>intval($_POST['pc_requested_count'] ?? 0)];
    $id = (new Customer())->create($data);
    (new \App\Models\Log())->addAction('create_customer', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$id, 'note'=>$data['email']]);
    \App\Services\ViewCardService::refreshCustomers();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/customers/'.$id);
  }
  // Visualizza form di modifica docente
  public function editForm($id) {
    Auth::require();
    $c = (new Customer())->find($id);
    Helpers::view('customers/form', ['title'=>'Modifica Docente','customer'=>$c]);
  }
  // Aggiorna docente, registra log e aggiorna le card
  public function update($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = ['first_name'=>trim($_POST['first_name'] ?? ''),'last_name'=>trim($_POST['last_name'] ?? ''),'email'=>trim($_POST['email'] ?? ''),'notes'=>trim($_POST['notes'] ?? ''),'pc_requested_count'=>intval($_POST['pc_requested_count'] ?? 0)];
    (new Customer())->update($id,$data);
    (new \App\Models\Log())->addAction('update_customer', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$id, 'note'=>$data['email']]);
    \App\Services\ViewCardService::refreshCustomers();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/customers/'.$id);
  }
  // Elimina docente, registra log e aggiorna le card
  public function delete($id) {
    Auth::require();
    if (!\App\Core\Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new \App\Models\Log())->addAction('delete_customer', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$id, 'note'=>strval($id)]);
    (new Customer())->delete($id);
    \App\Services\ViewCardService::refreshCustomers();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/customers');
  }

  // Esporta i docenti in CSV
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

  // Importa docenti da CSV e aggiorna le card
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
    \App\Services\ViewCardService::refreshCustomers();
    \App\Services\ViewCardService::refreshDashboard();
    Helpers::redirect('/customers');
  }
}
