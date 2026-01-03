<?php
namespace App\Services;
use App\Core\DB;
class ViewCardService {
  private static function upsert(string $scope, string $metric, int $value): void {
    $pdo = DB::conn();
    $stmt = $pdo->prepare("INSERT INTO view_cards (scope, metric, value, updated_at) VALUES (?,?,?,NOW()) ON DUPLICATE KEY UPDATE value=VALUES(value), updated_at=NOW()");
    $stmt->execute([$scope, $metric, $value]);
  }
  public static function refreshDashboard(): void {
    $pdo = DB::conn();
    $total = (int)$pdo->query('SELECT COUNT(*) c FROM laptops')->fetch()['c'];
    self::upsert('dashboard', 'laptops_total', $total);
    foreach (['ready','in_progress','missing_software','to_check','delivered'] as $st) {
      $stc = $pdo->prepare('SELECT COUNT(*) c FROM laptops WHERE status=?');
      $stc->execute([$st]);
      $cnt = (int)$stc->fetch()['c'];
      self::upsert('dashboard', $st, $cnt);
    }
    $customers = (int)$pdo->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];
    $students = (int)$pdo->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
    $groups = (int)$pdo->query('SELECT COUNT(*) c FROM work_groups')->fetch()['c'];
    self::upsert('dashboard', 'customers_total', $customers);
    self::upsert('dashboard', 'students_total', $students);
    self::upsert('dashboard', 'groups_total', $groups);
  }
  public static function refreshCustomers(): void {
    $pdo = DB::conn();
    $docenti = (int)$pdo->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];
    $pc_richiesti = (int)$pdo->query('SELECT COALESCE(SUM(pc_requested_count),0) s FROM customers')->fetch()['s'];
    $pc_assegnati = (int)$pdo->query('SELECT COUNT(*) c FROM laptops WHERE customer_id IS NOT NULL')->fetch()['c'];
    $pc_pagati = (int)$pdo->query("SELECT COALESCE(SUM(pcs_paid_count),0) s FROM payment_transfers WHERE status='verified'")->fetch()['s'];
    self::upsert('customers', 'docenti', $docenti);
    self::upsert('customers', 'pc_richiesti', $pc_richiesti);
    self::upsert('customers', 'pc_assegnati', $pc_assegnati);
    self::upsert('customers', 'pc_pagati', $pc_pagati);
  }
  public static function refreshStudents(): void {
    $pdo = DB::conn();
    $students_total = (int)$pdo->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
    $leaders_total = (int)$pdo->query("SELECT COUNT(DISTINCT student_id) c FROM group_members WHERE role='leader'")->fetch()['c'];
    $installers_total = (int)$pdo->query("SELECT COUNT(DISTINCT student_id) c FROM group_members WHERE role='installer'")->fetch()['c'];
    self::upsert('students', 'students', $students_total);
    self::upsert('students', 'leaders', $leaders_total);
    self::upsert('students', 'installers', $installers_total);
  }
  public static function refreshGroups(): void {
    $pdo = DB::conn();
    $groups_total = (int)$pdo->query('SELECT COUNT(*) c FROM work_groups')->fetch()['c'];
    $members_total = (int)$pdo->query('SELECT COUNT(*) c FROM group_members')->fetch()['c'];
    $laptops_total = (int)$pdo->query('SELECT COUNT(*) c FROM laptops WHERE group_id IS NOT NULL')->fetch()['c'];
    self::upsert('groups', 'groups', $groups_total);
    self::upsert('groups', 'students', $members_total);
    self::upsert('groups', 'laptops', $laptops_total);
  }
  public static function refreshPayments(): void {
    $pdo = DB::conn();
    $pcs_paid = (int)$pdo->query("SELECT COALESCE(SUM(pcs_paid_count),0) AS t FROM payment_transfers WHERE status='verified'")->fetch()['t'];
    $customers_cnt = (int)$pdo->query("SELECT COUNT(DISTINCT customer_id) AS c FROM payment_transfers WHERE status='verified'")->fetch()['c'];
    $pcs_requested = (int)$pdo->query("SELECT COALESCE(SUM(pc_requested_count),0) AS t FROM customers")->fetch()['t'];
    self::upsert('payments', 'pcs_paid', $pcs_paid);
    self::upsert('payments', 'customers', $customers_cnt);
    self::upsert('payments', 'pcs_requested', $pcs_requested);
  }
  public static function refreshLaptops(): void {
    $pdo = DB::conn();
    $total = (int)$pdo->query('SELECT COUNT(*) c FROM laptops')->fetch()['c'];
    $ready = (int)$pdo->query("SELECT COUNT(*) c FROM laptops WHERE status='ready'")->fetch()['c'];
    $in_work = $total - $ready;
    self::upsert('laptops', 'total', $total);
    self::upsert('laptops', 'ready', $ready);
    self::upsert('laptops', 'in_work', $in_work);
  }
  public static function refreshSoftware(): void {
    $pdo = DB::conn();
    $total = (int)$pdo->query('SELECT COUNT(*) c FROM software')->fetch()['c'];
    $free = (int)$pdo->query("SELECT COUNT(*) c FROM software WHERE cost IS NULL OR cost=0 OR LOWER(COALESCE(license,'')) LIKE 'free%'")->fetch()['c'];
    $paid = (int)$pdo->query("SELECT COUNT(*) c FROM software WHERE cost IS NOT NULL AND cost > 0")->fetch()['c'];
    self::upsert('software', 'total', $total);
    self::upsert('software', 'free', $free);
    self::upsert('software', 'paid', $paid);
  }
  public static function refreshAll(): void {
    self::refreshDashboard();
    self::refreshCustomers();
    self::refreshStudents();
    self::refreshGroups();
    self::refreshPayments();
    self::refreshLaptops();
    self::refreshSoftware();
  }
}
