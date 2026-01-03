<?php
/*
  File: Laptop.php
  Scopo: Modello per la gestione dei PC/Laptop.
  Spiegazione: Fornisce metodi CRUD, associazione software, storico stati e utilità.
*/
namespace App\Models;
class Laptop extends Model {
  // Ritorna tutti i laptop con filtri opzionali e join verso docente e gruppo
  public function all($filters = []) {
    $sql = 'SELECT l.*, c.first_name AS customer_first_name, c.last_name AS customer_last_name, wg.name AS group_name FROM laptops l LEFT JOIN customers c ON l.customer_id=c.id LEFT JOIN work_groups wg ON l.group_id=wg.id';
    $where = [];
    $params = [];
    if (!empty($filters['status'])) { $where[] = 'l.status = ?'; $params[] = $filters['status']; }
    if (!empty($filters['customer_id'])) { $where[] = 'l.customer_id = ?'; $params[] = $filters['customer_id']; }
    if (!empty($filters['group_id'])) { $where[] = 'l.group_id = ?'; $params[] = $filters['group_id']; }
    if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
    $sql .= ' ORDER BY l.code';
    $st = $this->pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
  }
  // Trova un laptop per ID con informazioni di docente e gruppo
  public function find($id) {
    $st = $this->pdo->prepare('SELECT l.*, c.first_name AS customer_first_name, c.last_name AS customer_last_name, wg.name AS group_name FROM laptops l LEFT JOIN customers c ON l.customer_id=c.id LEFT JOIN work_groups wg ON l.group_id=wg.id WHERE l.id=?');
    $st->execute([$id]);
    return $st->fetch();
  }
  // Trova un laptop per codice inventario
  public function findByCode($code) {
    $st = $this->pdo->prepare('SELECT * FROM laptops WHERE code=?');
    $st->execute([$code]);
    return $st->fetch();
  }
  // Crea un nuovo laptop
  public function create($data) {
    $st = $this->pdo->prepare('INSERT INTO laptops (code, brand_model, cpu, ram, storage, screen, tech_notes, scratches, physical_condition, battery, condition_level, office_license, windows_license, other_software_request, status, customer_id, group_id, last_operator_student_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $st->execute([
      $data['code'],$data['brand_model'],$data['cpu'],$data['ram'],$data['storage'],$data['screen'],$data['tech_notes'],$data['scratches'],$data['physical_condition'],$data['battery'],$data['condition_level'],$data['office_license'],$data['windows_license'],$data['other_software_request'],$data['status'],$data['customer_id'] ?: null,$data['group_id'] ?: null,$data['last_operator_student_id'] ?: null
    ]);
    return $this->pdo->lastInsertId();
  }
  // Aggiorna un laptop esistente
  public function update($id, $data) {
    $st = $this->pdo->prepare('UPDATE laptops SET code=?, brand_model=?, cpu=?, ram=?, storage=?, screen=?, tech_notes=?, scratches=?, physical_condition=?, battery=?, condition_level=?, office_license=?, windows_license=?, other_software_request=?, status=?, customer_id=?, group_id=?, last_operator_student_id=? WHERE id=?');
    $st->execute([
      $data['code'],$data['brand_model'],$data['cpu'],$data['ram'],$data['storage'],$data['screen'],$data['tech_notes'],$data['scratches'],$data['physical_condition'],$data['battery'],$data['condition_level'],$data['office_license'],$data['windows_license'],$data['other_software_request'],$data['status'],$data['customer_id'] ?: null,$data['group_id'] ?: null,$data['last_operator_student_id'] ?: null,$id
    ]);
  }
  // Elimina un laptop
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM laptops WHERE id=?');
    $st->execute([$id]);
  }
  // Ritorna gli ID software associati a un laptop
  public function softwareIds($laptop_id) {
    $st = $this->pdo->prepare('SELECT software_id FROM laptop_software WHERE laptop_id=?');
    $st->execute([$laptop_id]);
    return array_map(function($r){ return (int)$r['software_id']; }, $st->fetchAll());
  }
  // Imposta la lista software associata al laptop
  public function setSoftwares($laptop_id, $ids) {
    $this->pdo->prepare('DELETE FROM laptop_software WHERE laptop_id=?')->execute([$laptop_id]);
    if (!$ids) return;
    $ins = $this->pdo->prepare('INSERT INTO laptop_software (laptop_id, software_id) VALUES (?, ?)');
    foreach ($ids as $sid) { $ins->execute([$laptop_id, $sid]); }
  }
  // Elenco software (dettagli) associati al laptop
  public function softwareList($laptop_id) {
    $st = $this->pdo->prepare('SELECT s.* FROM laptop_software ls JOIN software s ON ls.software_id=s.id WHERE ls.laptop_id=? ORDER BY s.name');
    $st->execute([$laptop_id]);
    return $st->fetchAll();
  }
  // Storico cambi di stato del laptop
  public function stateHistory($laptop_id) {
    $st = $this->pdo->prepare('SELECT h.*, s.first_name, s.last_name FROM laptop_state_history h LEFT JOIN students s ON h.changed_by_student_id=s.id WHERE h.laptop_id=? ORDER BY h.created_at DESC');
    $st->execute([$laptop_id]);
    return $st->fetchAll();
  }
  // Aggiunge una voce allo storico stato
  public function addStateHistory($laptop_id, $student_id, $previous_status, $new_status, $note = null) {
    $st = $this->pdo->prepare('INSERT INTO laptop_state_history (laptop_id, changed_by_student_id, previous_status, new_status, note) VALUES (?,?,?,?,?)');
    $st->execute([$laptop_id, $student_id, $previous_status, $new_status, $note]);
  }
  // Registra un cambio di stato nei log azione
  public function logStatusChange($laptop_id, $actor_student_id, $customer_id = null, $group_id = null, $note = null) {
    $st = $this->pdo->prepare('INSERT INTO action_logs (actor_student_id, action_type, laptop_id, customer_id, group_id, note) VALUES (?,?,?,?,?,?)');
    $st->execute([$actor_student_id, 'change_laptop_status', $laptop_id, $customer_id, $group_id, $note]);
  }
}

