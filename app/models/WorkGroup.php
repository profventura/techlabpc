<?php
namespace App\Models;
class WorkGroup extends Model {
  public function all() {
    $st = $this->pdo->query('SELECT wg.*, s.first_name AS leader_first_name, s.last_name AS leader_last_name FROM work_groups wg JOIN students s ON wg.leader_student_id=s.id ORDER BY wg.name');
    return $st->fetchAll();
  }
  public function find($id) {
    $st = $this->pdo->prepare('SELECT wg.*, s.first_name AS leader_first_name, s.last_name AS leader_last_name FROM work_groups wg JOIN students s ON wg.leader_student_id=s.id WHERE wg.id=?');
    $st->execute([$id]);
    return $st->fetch();
  }
  public function members($id) {
    $st = $this->pdo->prepare('SELECT gm.*, st.first_name, st.last_name, st.email FROM group_members gm JOIN students st ON gm.student_id=st.id WHERE gm.group_id=? ORDER BY gm.role DESC, st.last_name');
    $st->execute([$id]);
    return $st->fetchAll();
  }
  public function laptops($id) {
    $st = $this->pdo->prepare('SELECT * FROM laptops WHERE group_id=? ORDER BY status, code');
    $st->execute([$id]);
    return $st->fetchAll();
  }
  public function create($data) {
    $st = $this->pdo->prepare('INSERT INTO work_groups (name, leader_student_id) VALUES (?,?)');
    $st->execute([$data['name'],$data['leader_student_id']]);
    return $this->pdo->lastInsertId();
  }
  public function update($id,$data) {
    $st = $this->pdo->prepare('UPDATE work_groups SET name=?, leader_student_id=? WHERE id=?');
    $st->execute([$data['name'],$data['leader_student_id'],$id]);
  }
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM work_groups WHERE id=?');
    $st->execute([$id]);
  }
  public function addMember($group_id,$student_id,$role) {
    $st = $this->pdo->prepare('INSERT IGNORE INTO group_members (group_id, student_id, role) VALUES (?,?,?)');
    $st->execute([$group_id,$student_id,$role]);
  }
  public function removeMember($group_id,$student_id) {
    $st = $this->pdo->prepare('DELETE FROM group_members WHERE group_id=? AND student_id=?');
    $st->execute([$group_id,$student_id]);
  }
}

