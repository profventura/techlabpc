<?php
namespace App\Models;
class WorkGroup extends Model {
  public function all() {
    $sql = 'SELECT wg.*, s.first_name AS leader_first_name, s.last_name AS leader_last_name,
      COALESCE(gm.members_count,0) AS members_count,
      COALESCE(lp.laptops_count,0) AS laptops_count
      FROM work_groups wg
      JOIN students s ON wg.leader_student_id = s.id
      LEFT JOIN (
        SELECT group_id, COUNT(*) AS members_count
        FROM group_members
        GROUP BY group_id
      ) gm ON gm.group_id = wg.id
      LEFT JOIN (
        SELECT group_id, COUNT(*) AS laptops_count
        FROM laptops
        GROUP BY group_id
      ) lp ON lp.group_id = wg.id
      ORDER BY wg.name';
    $st = $this->pdo->query($sql);
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
  public function setLeader($group_id,$student_id) {
    try {
      $this->pdo->beginTransaction();
      $this->pdo->prepare('UPDATE group_members SET role=\'installer\' WHERE group_id=? AND role=\'leader\'')->execute([$group_id]);
      $this->pdo->prepare('INSERT INTO group_members (group_id, student_id, role) VALUES (?,?,\'leader\') ON DUPLICATE KEY UPDATE role=\'leader\'')->execute([$group_id,$student_id]);
      $this->pdo->prepare('UPDATE work_groups SET leader_student_id=? WHERE id=?')->execute([$student_id,$group_id]);
      $this->pdo->commit();
    } catch (\Throwable $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }
  public function isLeader($group_id,$student_id) {
    $st = $this->pdo->prepare('SELECT role FROM group_members WHERE group_id=? AND student_id=?');
    $st->execute([$group_id,$student_id]);
    $r = $st->fetch();
    return $r && $r['role'] === 'leader';
  }
  public function leaderCount($group_id) {
    $st = $this->pdo->prepare('SELECT COUNT(*) c FROM group_members WHERE group_id=? AND role=\'leader\'');
    $st->execute([$group_id]);
    return (int)($st->fetch()['c'] ?? 0);
  }
  public function memberGroupOf($student_id) {
    $st = $this->pdo->prepare('SELECT group_id FROM group_members WHERE student_id=? LIMIT 1');
    $st->execute([$student_id]);
    $r = $st->fetch();
    return $r ? (int)$r['group_id'] : null;
  }
}

