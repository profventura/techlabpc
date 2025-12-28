<?php
namespace App\Models;
class Log extends Model {
  public function accessLogs() {
    $sql = 'SELECT a.*, s.first_name, s.last_name FROM access_logs a LEFT JOIN students s ON a.student_id=s.id ORDER BY a.created_at DESC';
    return $this->pdo->query($sql)->fetchAll();
  }
  public function actionLogs() {
    $sql = 'SELECT l.*, s.first_name, s.last_name, lp.code AS laptop_code, c.first_name AS customer_first_name, c.last_name AS customer_last_name, g.name AS group_name
            FROM action_logs l
            LEFT JOIN students s ON l.actor_student_id=s.id
            LEFT JOIN laptops lp ON l.laptop_id=lp.id
            LEFT JOIN customers c ON l.customer_id=c.id
            LEFT JOIN work_groups g ON l.group_id=g.id
            ORDER BY l.created_at DESC';
    return $this->pdo->query($sql)->fetchAll();
  }
}
