<?php
namespace App\Models;
class Student extends Model {
  public function all() {
    $st = $this->pdo->query('SELECT id, first_name, last_name, email, role, active FROM students ORDER BY last_name, first_name');
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

