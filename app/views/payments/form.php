<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo htmlspecialchars($title ?? 'Bonifico'); ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url(isset($payment['id']) ? '/payments/'.$payment['id'].'/update' : '/payments'); ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Docente</label>
            <select class="form-select" name="customer_id" id="customer_id" required>
              <?php foreach ($customers as $c) { ?>
                <option value="<?php echo $c['id']; ?>" <?php echo (isset($payment['customer_id']) && $payment['customer_id']==$c['id'])?'selected':''; ?>><?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?></option>
              <?php } ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Importo</label>
            <input type="number" step="0.01" class="form-control" name="amount" required value="<?php echo htmlspecialchars($payment['amount']??''); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Data</label>
            <input type="date" class="form-control" name="paid_at" required value="<?php echo htmlspecialchars($payment['paid_at']??''); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Riferimento</label>
            <input type="text" class="form-control" name="reference" value="<?php echo htmlspecialchars($payment['reference']??''); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Contabile</label>
            <input type="file" class="form-control" name="receipt" accept=".pdf,.jpg,.png">
            <?php if (!empty($payment['receipt_path'])) { ?>
              <div class="form-text">Contabile attuale: <a href="<?php echo \App\Core\Helpers::url($payment['receipt_path']); ?>" target="_blank">Apri</a></div>
            <?php } ?>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Stato</label>
            <select class="form-select" name="status">
              <?php foreach (['pending','verified','rejected'] as $st) { ?>
                <option value="<?php echo $st; ?>" <?php echo (($payment['status']??'pending')===$st)?'selected':''; ?>><?php echo $st; ?></option>
              <?php } ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="pcs_paid_count">PC pagati</label>
            <input type="number" class="form-control" id="pcs_paid_count" name="pcs_paid_count" min="0" aria-label="Numero di PC pagati" value="<?php echo htmlspecialchars($payment['pcs_paid_count']??''); ?>">
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><?php echo isset($payment['id']) ? 'Aggiorna' : 'Salva'; ?></button>
      <a href="<?php echo \App\Core\Helpers::url('/payments'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
