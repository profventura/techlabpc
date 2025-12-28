<?php
namespace App\Models;
class Customer extends Model {
  public function all() {
    $st = $this->pdo->query('SELECT * FROM customers ORDER BY last_name, first_name');
    return $st->fetchAll();
  }
  public function find($id) {
    $st = $this->pdo->prepare('SELECT * FROM customers WHERE id=?');
    $st->execute([$id]);
    return $st->fetch();
  }
  public function laptops($id) {
    $st = $this->pdo->prepare('SELECT * FROM laptops WHERE customer_id=? ORDER BY code');
    $st->execute([$id]);
    return $st->fetchAll();
  }
  public function payments($id) {
    $st = $this->pdo->prepare('SELECT * FROM payment_transfers WHERE customer_id=? ORDER BY paid_at DESC');
    $st->execute([$id]);
    return $st->fetchAll();
  }
  public function create($data) {
    $st = $this->pdo->prepare('INSERT INTO customers (first_name,last_name,email,notes) VALUES (?,?,?,?)');
    $st->execute([$data['first_name'],$data['last_name'],$data['email'],$data['notes']]);
    return $this->pdo->lastInsertId();
  }
  public function update($id,$data) {
    $st = $this->pdo->prepare('UPDATE customers SET first_name=?, last_name=?, email=?, notes=? WHERE id=?');
    $st->execute([$data['first_name'],$data['last_name'],$data['email'],$data['notes'],$id]);
  }
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM customers WHERE id=?');
    $st->execute([$id]);
  }
}

