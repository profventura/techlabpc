<?php
namespace App\Models;
class Student extends Model {
  public function all($filters = []) {
    $sql = 'SELECT s.id, s.first_name, s.last_name, s.email, s.role, s.active,
                   GROUP_CONCAT(DISTINCT wg.name ORDER BY wg.name SEPARATOR \', \') AS group_names,
                   GROUP_CONCAT(DISTINCT gm.role ORDER BY gm.role SEPARATOR \', \') AS group_roles
            FROM students s
            LEFT JOIN group_members gm ON gm.student_id = s.id
            LEFT JOIN work_groups wg ON gm.group_id = wg.id';
    $where = [];
    $params = [];
    if (!empty($filters['role'])) { $where[] = 's.role = ?'; $params[] = $filters['role']; }
    if ($filters['active'] !== null && $filters['active'] !== '') { $where[] = 's.active = ?'; $params[] = (int)$filters['active']; }
    if (!empty($filters['group_id'])) { $where[] = 'gm.group_id = ?'; $params[] = (int)$filters['group_id']; }
    if (!empty($filters['q'])) { $where[] = '(s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ?)'; $params[] = '%'.$filters['q'].'%'; $params[] = '%'.$filters['q'].'%'; $params[] = '%'.$filters['q'].'%'; }
    if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
    $sql .= ' GROUP BY s.id ORDER BY s.last_name, s.first_name';
    $st = $this->pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
  }
  public function withoutGroup() {
    $sql = 'SELECT s.id, s.first_name, s.last_name, s.email, s.role, s.active
            FROM students s
            LEFT JOIN group_members gm ON gm.student_id = s.id
            WHERE gm.student_id IS NULL
            ORDER BY s.last_name, s.first_name';
    $st = $this->pdo->query($sql);
    return $st->fetchAll();
  }
  public function find($id) {
    $st = $this->pdo->prepare('SELECT id, first_name, last_name, email, role, active FROM students WHERE id=?');
    $st->execute([$id]);
    return $st->fetch();
  }
  public function create($data) {
    $st = $this->pdo->prepare('INSERT INTO students (first_name,last_name,email,password_hash,role,active) VALUES (?,?,?,?,?,?)');
    $st->execute([$data['first_name'],$data['last_name'],$data['email'],password_hash($data['password'], PASSWORD_DEFAULT),$data['role'],$data['active']]);
    return $this->pdo->lastInsertId();
  }
  public function update($id,$data) {
    if (!empty($data['password'])) {
      $st = $this->pdo->prepare('UPDATE students SET first_name=?, last_name=?, email=?, role=?, active=?, password_hash=? WHERE id=?');
      $st->execute([$data['first_name'],$data['last_name'],$data['email'],$data['role'],$data['active'],password_hash($data['password'], PASSWORD_DEFAULT),$id]);
    } else {
      $st = $this->pdo->prepare('UPDATE students SET first_name=?, last_name=?, email=?, role=?, active=? WHERE id=?');
      $st->execute([$data['first_name'],$data['last_name'],$data['email'],$data['role'],$data['active'],$id]);
    }
  }
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM students WHERE id=?');
    $st->execute([$id]);
  }
}

