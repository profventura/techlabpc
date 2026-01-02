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
  public function addAction($type, $actor_student_id, $payload = []) {
    $actor = (isset($actor_student_id) && is_numeric($actor_student_id) && (int)$actor_student_id > 0) ? (int)$actor_student_id : null;
    $laptop = (isset($payload['laptop_id']) && is_numeric($payload['laptop_id']) && (int)$payload['laptop_id'] > 0) ? (int)$payload['laptop_id'] : null;
    $customer = (isset($payload['customer_id']) && is_numeric($payload['customer_id']) && (int)$payload['customer_id'] > 0) ? (int)$payload['customer_id'] : null;
    $group = (isset($payload['group_id']) && is_numeric($payload['group_id']) && (int)$payload['group_id'] > 0) ? (int)$payload['group_id'] : null;
    $note = isset($payload['note']) ? $payload['note'] : null;
    $st = $this->pdo->prepare('INSERT INTO action_logs (actor_student_id, action_type, laptop_id, customer_id, group_id, note) VALUES (?,?,?,?,?,?)');
    $st->execute([$actor, $type, $laptop, $customer, $group, $note]);
  }
}
