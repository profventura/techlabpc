<?php
/*
  File: LogsController.php
  Scopo: Gestisce la pagina dei log di accesso e di azione, con funzioni di svuotamento (solo admin).
  Spiegazione: Recupera i log dal modello Log e fornisce azioni per cancellare le tabelle di log.
*/
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Log;
class LogsController {
  // Mostra la pagina Logs con due tabelle (accessi, azioni) e controlli DataTables
  public function index() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    $m = new Log();
    $access = $m->accessLogs();
    $actions = $m->actionLogs();
    $lenAccess = isset($_GET['len_access']) ? (strtolower(strval($_GET['len_access']))==='all' ? -1 : (int)$_GET['len_access']) : 10;
    $lenActions = isset($_GET['len_actions']) ? (strtolower(strval($_GET['len_actions']))==='all' ? -1 : (int)$_GET['len_actions']) : 10;
    if ($lenAccess === 0) $lenAccess = 10;
    if ($lenActions === 0) $lenActions = 10;
    Helpers::view('logs/index', ['title'=>'Logs','access'=>$access,'actions'=>$actions,'len_access'=>$lenAccess,'len_actions'=>$lenActions]);
  }
  // Svuota i log di accesso (access_logs), protetto da CSRF e permesso admin
  public function clearAccess() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $pdo = \App\Core\DB::conn();
    $pdo->exec('DELETE FROM access_logs');
    Helpers::addFlash('success', 'Logs accessi svuotati');
    Helpers::redirect('/logs');
  }
  // Svuota i log di azione (action_logs), protetto da CSRF e permesso admin
  public function clearActions() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $pdo = \App\Core\DB::conn();
    $pdo->exec('DELETE FROM action_logs');
    Helpers::addFlash('success', 'Log azioni svuotati');
    Helpers::redirect('/logs');
  }
}
