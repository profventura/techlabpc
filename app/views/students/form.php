<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo $student ? 'Modifica Studente' : 'Nuovo Studente'; ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url($student ? '/students/'.$student['id'].'/update' : '/students'); ?>">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nome</label>
          <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($student['first_name']??''); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Cognome</label>
          <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($student['last_name']??''); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']??''); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password" placeholder="<?php echo $student ? 'Lascia vuoto per non cambiare' : ''; ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Ruolo</label>
          <select class="form-select" name="role">
            <option value="student" <?php echo (($student['role']??'student')==='student')?'selected':''; ?>>Studente</option>
            <option value="admin" <?php echo (($student['role']??'student')==='admin')?'selected':''; ?>>Admin</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="active" id="activeCheck" <?php echo (($student['active']??1)?'checked':''); ?>>
            <label class="form-check-label" for="activeCheck">Attivo</label>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="<?php echo \App\Core\Helpers::url('/students'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
