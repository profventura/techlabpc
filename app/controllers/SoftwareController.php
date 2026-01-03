<?php
/*
  File: SoftwareController.php
  Scopo: Gestisce il catalogo dei Software (lista, creazione, modifica, eliminazione).
  Spiegazione: Coordina il modello Software e aggiorna le card riepilogative dello scope software.
*/
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Software;
class SoftwareController {
  // Lista software con riepilogo card
  public function index() {
    Auth::require();
    $items = (new Software())->all();
    $pdo = \App\Core\DB::conn();
    $stmt = $pdo->prepare('SELECT metric, value FROM view_cards WHERE scope=?');
    $stmt->execute(['software']);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      \App\Services\ViewCardService::refreshSoftware();
      $stmt->execute(['software']);
      $rows = $stmt->fetchAll();
    }
    if (!$rows) {
      $rows = [];
    }
    $m = [];
    foreach ($rows as $r) { $m[$r['metric']] = (int)$r['value']; }
    if (!isset($m['total']) || !isset($m['free']) || !isset($m['paid'])) {
      \App\Services\ViewCardService::refreshSoftware();
      $stmt->execute(['software']);
      $rows = $stmt->fetchAll();
      $m = [];
      foreach ($rows as $r) { $m[$r['metric']] = (int)$r['value']; }
    }
    $summary = ['total'=>$m['total'] ?? 0,'free'=>$m['free'] ?? 0,'paid'=>$m['paid'] ?? 0];
    Helpers::view('software/index', ['title'=>'Software','items'=>$items,'summary'=>$summary]);
  }
  // Visualizza form di creazione nuovo software
  public function createForm() {
    Auth::require();
    Helpers::view('software/form', ['title'=>'Nuovo Software','item'=>null]);
  }
  // Salva nuovo software e aggiorna card
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
    (new \App\Models\Log())->addAction('create_software', \App\Core\Auth::user()['id'] ?? null, ['note'=>$data['name'].' '.$data['version']]);
    \App\Services\ViewCardService::refreshSoftware();
    Helpers::redirect('/software/'.$id.'/edit');
  }
  // Visualizza form di modifica software
  public function editForm($id) {
    Auth::require();
    $item = (new Software())->find($id);
    Helpers::view('software/form', ['title'=>'Modifica Software','item'=>$item]);
  }
  // Aggiorna software e registra log
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
    (new \App\Models\Log())->addAction('update_software', \App\Core\Auth::user()['id'] ?? null, ['note'=>$data['name'].' '.$data['version']]);
    \App\Services\ViewCardService::refreshSoftware();
    Helpers::redirect('/software');
  }
  // Elimina software, registra log e aggiorna card
  public function delete($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Software())->delete($id);
    (new \App\Models\Log())->addAction('delete_software', \App\Core\Auth::user()['id'] ?? null, ['note'=>strval($id)]);
    \App\Services\ViewCardService::refreshSoftware();
    Helpers::redirect('/software');
  }
}
