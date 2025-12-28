<?php
namespace App\Models;
class Software extends Model {
  public function all() {
    $st = $this->pdo->query('SELECT * FROM software ORDER BY name');
    return $st->fetchAll();
  }
  public function find($id) {
    $st = $this->pdo->prepare('SELECT * FROM software WHERE id=?');
    $st->execute([$id]);
    return $st->fetch();
  }
  public function create($data) {
    $st = $this->pdo->prepare('INSERT INTO software (name, version, license, notes, cost) VALUES (?,?,?,?,?)');
    $st->execute([
      $data['name'], $data['version'] ?: null, $data['license'] ?: null, $data['notes'] ?: null, $data['cost'] !== '' ? $data['cost'] : null
    ]);
    return $this->pdo->lastInsertId();
  }
  public function update($id, $data) {
    $st = $this->pdo->prepare('UPDATE software SET name=?, version=?, license=?, notes=?, cost=? WHERE id=?');
    $st->execute([
      $data['name'], $data['version'] ?: null, $data['license'] ?: null, $data['notes'] ?: null, $data['cost'] !== '' ? $data['cost'] : null, $id
    ]);
  }
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM software WHERE id=?');
    $st->execute([$id]);
  }
}
