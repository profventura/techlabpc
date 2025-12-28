<?php
namespace App\Core;
use App\Core\DB;
class Auth {
  public static function user() { return $_SESSION['user'] ?? null; }
  public static function check() { return isset($_SESSION['user']); }
  public static function require() { if (!self::check()) { Helpers::redirect('/login'); } }
  public static function login($email, $password) {
    $pdo = DB::conn();
    $stmt = $pdo->prepare('SELECT * FROM students WHERE email = ? AND active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    $ok = $user && password_verify($password, $user['password_hash']);
    self::logAccess($ok ? 'login_ok' : 'login_ko', $user ? $user['id'] : null);
    if ($ok) { $_SESSION['user'] = ['id'=>$user['id'],'email'=>$user['email'],'role'=>$user['role'],'name'=>$user['first_name'].' '.$user['last_name']]; return true; }
    return false;
  }
  public static function logout() {
    $u = self::user();
    self::logAccess('logout', $u ? $u['id'] : null);
    unset($_SESSION['user']);
  }
  private static function ip() { return $_SERVER['REMOTE_ADDR'] ?? null; }
  private static function logAccess($event, $student_id) {
    $pdo = DB::conn();
    $stmt = $pdo->prepare('INSERT INTO access_logs (student_id, event, ip) VALUES (?,?,?)');
    $stmt->execute([$student_id, $event, self::ip()]);
  }
  public static function isAdmin() { $u = self::user(); return $u && $u['role'] === 'admin'; }
}

