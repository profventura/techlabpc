<h3 class="mb-3">Pagamenti bonifico</h3>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/payments/create'); ?>">Nuovo Bonifico</a></div>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Docente</th><th>Data</th><th>Importo</th><th>Stato</th><th>Azioni</th></tr></thead>
    <tbody>
    <?php $__pay_labels = ['pending'=>'In attesa','verified'=>'Verificato','rejected'=>'Rifiutato']; foreach ($payments as $p) { ?>
      <tr>
        <td><?php echo htmlspecialchars($p['last_name'].' '.$p['first_name']); ?></td>
        <td><?php echo htmlspecialchars($p['paid_at']); ?></td>
        <td>€<?php echo number_format($p['amount'],2,',','.'); ?></td>
        <?php $st = $p['status']; ?>
        <td><span class="badge bg-<?php echo $st=='verified'?'success':($st=='rejected'?'danger':'warning'); ?>"><?php echo htmlspecialchars($__pay_labels[$st] ?? $st); ?></span></td>
        <td>
          <?php if (!empty($p['receipt_path'])) { ?>
            <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url($p['receipt_path']); ?>" target="_blank">Apri</a>
          <?php } else { ?>
            <span class="badge bg-danger">Mancante</span>
          <?php } ?>
          <?php if (\App\Core\Auth::isAdmin()) { ?>
          <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deletePaymentModal" data-id="<?php echo $p['id']; ?>">Elimina</button>
          <?php } ?>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<div class="modal fade" id="deletePaymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deletePaymentForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di voler eliminare questo pagamento?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-danger">Elimina</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.getElementById('deletePaymentModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    document.getElementById('deletePaymentForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/payments/'); ?>' + id + '/delete');
  });
</script>

