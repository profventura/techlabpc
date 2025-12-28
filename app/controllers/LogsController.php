<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Helpers;
use App\Models\Log;
class LogsController {
  public function index() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    $m = new Log();
    $access = $m->accessLogs();
    $actions = $m->actionLogs();
    Helpers::view('logs/index', ['title'=>'Logs','access'=>$access,'actions'=>$actions]);
  }
}
