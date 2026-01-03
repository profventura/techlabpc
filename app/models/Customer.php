<?php
namespace App\Models;
class Customer extends Model {
  public function all() {
    $sql = 'SELECT c.*,
      COALESCE(l.laptops_count,0) AS laptops_count,
      COALESCE(pt.pcs_paid_total,0) AS pcs_paid_total
      FROM customers c
      LEFT JOIN (
        SELECT customer_id, COUNT(*) AS laptops_count
        FROM laptops
        GROUP BY customer_id
      ) l ON l.customer_id = c.id
      LEFT JOIN (
        SELECT customer_id, COALESCE(SUM(pcs_paid_count),0) AS pcs_paid_total
        FROM payment_transfers
        WHERE status = \'verified\'
        GROUP BY customer_id
      ) pt ON pt.customer_id = c.id
      ORDER BY c.last_name, c.first_name';
    $st = $this->pdo->query($sql);
    return $st->fetchAll();
  }
  public function availableForLaptop($include_id = null) {
    $sql = 'SELECT c.*, (SELECT COUNT(*) FROM laptops l WHERE l.customer_id=c.id) AS laptops_count
            FROM customers c
            WHERE (SELECT COUNT(*) FROM laptops l WHERE l.customer_id=c.id) < c.pc_requested_count';
    $st = $this->pdo->query($sql);
    $rows = $st->fetchAll();
    if ($include_id) {
      $one = $this->find($include_id);
      if ($one) {
        $exists = false;
        foreach ($rows as $r) { if ((int)$r['id'] === (int)$include_id) { $exists = true; break; } }
        if (!$exists) { $rows[] = $one; }
      }
    }
    usort($rows, function($a,$b){
      $la = trim(($a['last_name']??'').' '.($a['first_name']??'')); 
      $lb = trim(($b['last_name']??'').' '.($b['first_name']??'')); 
      return strcmp($la, $lb);
    });
    return $rows;
  }
  public function counts($customer_id) {
    $st = $this->pdo->prepare('SELECT pc_requested_count AS requested, (SELECT COUNT(*) FROM laptops l WHERE l.customer_id=?) AS assigned FROM customers WHERE id=?');
    $st->execute([$customer_id,$customer_id]);
    return $st->fetch() ?: ['requested'=>0,'assigned'=>0];
  }
  public function canAssignLaptop($customer_id, $current_laptop_customer_id = null) {
    if (!$customer_id) return true;
    if ($current_laptop_customer_id && (int)$customer_id === (int)$current_laptop_customer_id) return true;
    $c = $this->counts($customer_id);
    return (int)$c['assigned'] < (int)$c['requested'];
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
    $st = $this->pdo->prepare('INSERT INTO customers (first_name,last_name,email,notes,pc_requested_count) VALUES (?,?,?,?,?)');
    $st->execute([$data['first_name'],$data['last_name'],$data['email'],$data['notes'],$data['pc_requested_count'] ?? 0]);
    return $this->pdo->lastInsertId();
  }
  public function update($id,$data) {
    $st = $this->pdo->prepare('UPDATE customers SET first_name=?, last_name=?, email=?, notes=?, pc_requested_count=? WHERE id=?');
    $st->execute([$data['first_name'],$data['last_name'],$data['email'],$data['notes'],$data['pc_requested_count'] ?? 0,$id]);
  }
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM customers WHERE id=?');
    $st->execute([$id]);
  }
}

