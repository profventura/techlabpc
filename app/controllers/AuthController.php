<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
class AuthController {
  public function loginForm() {
    Helpers::view('login', ['title'=>'Login']);
  }
  public function login() {
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $res = Auth::login($email, $password);
    if ($res === true) {
      Helpers::redirect('/');
    } elseif ($res === 'denied_leader_only') {
      Helpers::view('login', ['title'=>'Login','error'=>'Credenziali corrette ma l’accesso è consentito solo ai responsabili dei gruppi o all’amministratore']);
    } else {
      Helpers::view('login', ['title'=>'Login','error'=>'Credenziali non valide']);
    }
  }
  public function logout() {
    Auth::logout();
    Helpers::redirect('/login');
  }
}

