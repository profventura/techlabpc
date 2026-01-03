<?php
/*
  File: Auth.php
  Scopo: Gestione autenticazione utenti (studenti/admin), login/logout e controllo permessi.
  Spiegazione: Fornisce metodi statici per verificare l’utente loggato, effettuare login,
  registrare gli accessi nei log e applicare regole di autorizzazione.
*/
namespace App\Core;
use App\Core\DB;
class Auth {
  // Ritorna i dati dell’utente corrente (o null se non loggato)
  public static function user() { return $_SESSION['user'] ?? null; }
  // Verifica se esiste una sessione attiva
  public static function check() { return isset($_SESSION['user']); }
  // Impone l’autenticazione: se non loggato, reindirizza al login
  public static function require() { if (!self::check()) { Helpers::redirect('/login'); } }
  /*
    Metodo: login
    Parametri: email, password
    Funzione: Autentica lo studente, consente accesso a admin o leader di gruppo,
    registra esito nei log di accesso e inizializza la sessione.
    Ritorno: true/false oppure stringa 'denied_leader_only' se non autorizzato.
  */
  public static function login($email, $password) {
    $pdo = DB::conn();
    $stmt = $pdo->prepare('SELECT * FROM students WHERE email = ? AND active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    $ok = $user && password_verify($password, $user['password_hash']);
    if (!$ok) {
      self::logAccess('login_ko', $user ? $user['id'] : null);
      return false;
    }
    $allowed = ($user['role'] === 'admin');
    if (!$allowed) {
      $chk = $pdo->prepare("SELECT COUNT(*) c FROM group_members WHERE student_id=? AND role='leader'");
      $chk->execute([$user['id']]);
      $allowed = ((int)$chk->fetch()['c']) > 0;
    }
    if (!$allowed) {
      self::logAccess('login_ko', $user['id']);
      return 'denied_leader_only';
    }
    self::logAccess('login_ok', $user['id']);
    $_SESSION['user'] = ['id'=>$user['id'],'email'=>$user['email'],'role'=>$user['role'],'name'=>$user['first_name'].' '.$user['last_name']];
    return true;
  }
  // Effettua logout e registra evento
  public static function logout() {
    $u = self::user();
    self::logAccess('logout', $u ? $u['id'] : null);
    unset($_SESSION['user']);
  }
  // Recupera IP del client
  private static function ip() { return $_SERVER['REMOTE_ADDR'] ?? null; }
  // Registra evento di accesso (login/logout) su access_logs
  private static function logAccess($event, $student_id) {
    $pdo = DB::conn();
    $stmt = $pdo->prepare('INSERT INTO access_logs (student_id, event, ip) VALUES (?,?,?)');
    $stmt->execute([$student_id, $event, self::ip()]);
  }
  // Verifica se l’utente corrente è admin
  public static function isAdmin() { $u = self::user(); return $u && $u['role'] === 'admin'; }
}

