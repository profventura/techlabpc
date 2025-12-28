<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4">Nuovo bonifico</h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url('/payments'); ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Docente</label>
            <select class="form-select" name="customer_id" required>
              <?php foreach ($customers as $c) { ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?></option>
              <?php } ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Importo</label>
            <input type="number" step="0.01" class="form-control" name="amount" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Data</label>
            <input type="date" class="form-control" name="paid_at" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Riferimento</label>
            <input type="text" class="form-control" name="reference">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Contabile</label>
            <input type="file" class="form-control" name="receipt" accept=".pdf,.jpg,.png">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Stato</label>
            <select class="form-select" name="status">
              <option value="pending">pending</option>
              <option value="verified">verified</option>
              <option value="rejected">rejected</option>
            </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="<?php echo \App\Core\Helpers::url('/payments'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
