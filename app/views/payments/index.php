<h3 class="mb-3">Pagamenti bonifico</h3>
<div class="row mb-3">
  <div class="col-md-4">
    <div class="card bg-secondary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:users-group-rounded-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero docenti</h5>
        <h2 class="card-text text-secondary text-center"><?php echo (int)($summary['customers'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-warning-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-warning flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:laptop-minimalistic-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero PC richiesti</h5>
        <h2 class="card-text text-warning text-center"><?php echo (int)($summary['pcs_requested'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-info-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-info flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:card-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero PC pagati</h5>
        <h2 class="card-text text-info text-center"><?php echo (int)($summary['pcs_paid'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
</div>
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
        { extend: 'copy', text: 'Copia', className: 'btn btn-outline-primary' },
        { extend: 'csv', text: 'CSV', className: 'btn btn-outline-primary' },
        { extend: 'excel', text: 'Excel', className: 'btn btn-outline-primary' },
        { extend: 'pdf', text: 'PDF', className: 'btn btn-outline-primary' },
        { extend: 'print', text: 'Stampa', className: 'btn btn-outline-primary' },
        { extend: 'colvis', text: 'Colonne', className: 'btn btn-outline-primary' }
      ],
      language: {
        search: 'Cerca:',
        lengthMenu: 'Mostra _MENU_ righe',
        info: 'Mostra da _START_ a _END_ di _TOTAL_',
        infoEmpty: 'Nessun record',
        zeroRecords: 'Nessun risultato trovato',
        loadingRecords: 'Caricamento...',
        processing: 'Elaborazione...',
        paginate: { first: 'Prima', last: 'Ultima', next: 'Successiva', previous: 'Precedente' }
      }
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
