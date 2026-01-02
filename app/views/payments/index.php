<h3 class="mb-3">Pagamenti bonifico</h3>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/payments/create'); ?>">Nuovo Bonifico</a></div>
<div class="table-responsive">
  <table id="paymentsTable" class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Docente</th><th>Data</th><th>Importo</th><th>PC pagati</th><th>Stato</th><th>Azioni</th></tr></thead>
    <tbody>
    <?php $__pay_labels = ['pending'=>'In attesa','verified'=>'Verificato','rejected'=>'Rifiutato']; foreach ($payments as $p) { ?>
      <tr>
        <td><?php echo htmlspecialchars($p['last_name'].' '.$p['first_name']); ?></td>
        <td><?php echo htmlspecialchars($p['paid_at']); ?></td>
        <td>€<?php echo number_format($p['amount'],2,',','.'); ?></td>
        <td><?php echo (int)($p['pcs_paid_count'] ?? 0); ?></td>
        <?php $st = $p['status']; ?>
        <td><span class="badge bg-<?php echo $st=='verified'?'success':($st=='rejected'?'danger':'warning'); ?>"><?php echo htmlspecialchars($__pay_labels[$st] ?? $st); ?></span></td>
        <td>
          <a class="btn btn-sm btn-outline-secondary ms-1" href="<?php echo \App\Core\Helpers::url('/payments/'.$p['id'].'/edit'); ?>">Modifica</a>
          <?php if (\App\Core\Auth::isAdmin()) { ?>
          <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deletePaymentModal" data-id="<?php echo $p['id']; ?>">Elimina</button>
          <?php } ?>
          <?php if (!empty($p['receipt_path'])) { ?>
            <a class="btn btn-sm btn-outline-primary ms-1" href="<?php echo \App\Core\Helpers::url($p['receipt_path']); ?>" target="_blank">Visualizza</a>
          <?php } else { ?>
            <span class="badge bg-danger ms-1">Mancante</span>
          <?php } ?>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    if (!window.jQuery) return;
    var $ = window.jQuery;
    $('#paymentsTable').DataTable({
      responsive: true,
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
      order: [],
      columnDefs: [
        { targets: -1, orderable: false, searchable: false }
      ],
      dom: 'Bfrtip',
      buttons: [
        { extend: 'copy', className: 'btn btn-outline-primary' },
        { extend: 'csv', className: 'btn btn-outline-primary' },
        { extend: 'excel', className: 'btn btn-outline-primary' },
        { extend: 'pdf', className: 'btn btn-outline-primary' },
        { extend: 'print', className: 'btn btn-outline-primary' },
        { extend: 'colvis', className: 'btn btn-outline-primary' }
      ]
    });
    var t = document.getElementById('paymentsTable');
    var wid = t.id + '_search';
    var wrap = $(t).closest('.dataTables_wrapper');
    var lbl = wrap.find('.dataTables_filter label');
    var inp = lbl.find('input');
    inp.attr({ id: wid, name: wid, 'aria-label': 'Cerca pagamenti' });
    lbl.attr('for', wid);
    var lsel = wrap.find('.dataTables_length select');
    var lid = t.id + '_length';
    lsel.attr({ id: lid, name: lid, 'aria-label': 'Numero righe' });
  });
</script>
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

