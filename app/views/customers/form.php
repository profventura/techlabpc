<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo $customer ? 'Modifica Docente' : 'Nuovo Docente'; ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url($customer ? '/customers/'.$customer['id'].'/update' : '/customers'); ?>">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nome</label>
          <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']??''); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Cognome</label>
          <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']??''); ?>" required>
        </div>
        <div class="col-md-12 mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customer['email']??''); ?>" required>
        </div>
        <div class="col-md-12 mb-3">
          <label class="form-label">Note</label>
          <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($customer['notes']??''); ?></textarea>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="<?php echo \App\Core\Helpers::url('/customers'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
