<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo $group ? 'Modifica Gruppo' : 'Nuovo Gruppo'; ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url($group ? '/work-groups/'.$group['id'].'/update' : '/work-groups'); ?>">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Nome gruppo</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($group['name']??''); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Responsabile</label>
            <select class="form-select" name="leader_student_id" required>
              <?php foreach ($students as $s) { ?>
                <option value="<?php echo $s['id']; ?>" <?php echo (($group['leader_student_id']??'')==$s['id'])?'selected':''; ?>><?php echo htmlspecialchars($s['last_name'].' '.$s['first_name']); ?></option>
              <?php } ?>
            </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="<?php echo \App\Core\Helpers::url('/work-groups'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
