<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo $customer ? 'Modifica Docente' : 'Nuovo Docente'; ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url($customer ? '/customers/'.$customer['id'].'/update' : '/customers'); ?>">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="first_name">Nome</label>
          <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']??''); ?>" required autocomplete="given-name">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="last_name">Cognome</label>
          <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']??''); ?>" required autocomplete="family-name">
        </div>
        <div class="col-md-12 mb-3">
          <label class="form-label" for="customer_email">Email</label>
          <input type="email" class="form-control" id="customer_email" name="email" value="<?php echo htmlspecialchars($customer['email']??''); ?>" required autocomplete="email" inputmode="email">
        </div>
        <div class="col-md-12 mb-3">
          <label class="form-label" for="pc_requested_count">PC richiesti dal docente</label>
          <input type="number" class="form-control" id="pc_requested_count" name="pc_requested_count" value="<?php echo htmlspecialchars((string)($customer['pc_requested_count']??0)); ?>" min="0" autocomplete="off">
        </div>
        <div class="col-md-12 mb-3">
          <label class="form-label" for="customer_notes">Note</label>
          <textarea class="form-control" id="customer_notes" name="notes" rows="3" autocomplete="off"><?php echo htmlspecialchars($customer['notes']??''); ?></textarea>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="<?php echo \App\Core\Helpers::url('/customers'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
