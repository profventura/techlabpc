<?php
namespace App\Models;
class PaymentTransfer extends Model {
  public function all() {
    $st = $this->pdo->query('SELECT p.*, c.first_name, c.last_name FROM payment_transfers p JOIN customers c ON p.customer_id=c.id ORDER BY paid_at DESC');
    return $st->fetchAll();
  }
  public function find($id) {
    $st = $this->pdo->prepare('SELECT * FROM payment_transfers WHERE id=?');
    $st->execute([$id]);
    return $st->fetch();
  }
  public function create($data) {
    $st = $this->pdo->prepare('INSERT INTO payment_transfers (customer_id, amount, paid_at, reference, receipt_path, pcs_paid_count, status) VALUES (?,?,?,?,?,?,?)');
    $st->execute([$data['customer_id'],$data['amount'],$data['paid_at'],$data['reference'],$data['receipt_path'],$data['pcs_paid_count'],$data['status']]);
    return $this->pdo->lastInsertId();
  }
  public function update($id, $data) {
    $st = $this->pdo->prepare('UPDATE payment_transfers SET customer_id=?, amount=?, paid_at=?, reference=?, receipt_path=?, pcs_paid_count=?, status=? WHERE id=?');
    $st->execute([$data['customer_id'],$data['amount'],$data['paid_at'],$data['reference'],$data['receipt_path'],$data['pcs_paid_count'],$data['status'],$id]);
  }
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM payment_transfers WHERE id=?');
    $st->execute([$id]);
  }
}
