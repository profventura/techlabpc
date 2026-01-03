<h3 class="mb-3">Docenti</h3>
<div class="row mb-3">
  <div class="col-md-3">
    <div class="card bg-secondary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:users-group-rounded-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero docenti</h5>
        <h2 class="card-text text-secondary text-center"><?php echo (int)($summary['docenti'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-warning-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-warning flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:laptop-minimalistic-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero PC richiesti</h5>
        <h2 class="card-text text-warning text-center"><?php echo (int)($summary['pc_richiesti'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-secondary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:laptop-minimalistic-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero PC assegnati</h5>
        <h2 class="card-text text-secondary text-center"><?php echo (int)($summary['pc_assegnati'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-info-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-info flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:card-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero PC pagati</h5>
        <h2 class="card-text text-info text-center"><?php echo (int)($summary['pc_pagati'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
</div>
<div class="d-flex justify-content-end mb-3 gap-2">
  <a class="btn btn-outline-success export-csv" data-count="<?php echo count($customers); ?>" href="<?php echo \App\Core\Helpers::url('/customers/export'); ?>">Esporta CSV</a>
  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">Importa CSV</button>
  <a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/customers/create'); ?>">Nuovo Docente</a>
</div>
<div class="table-responsive">
  <table id="customersTable" class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Nome</th><th>Email</th><th>PC richiesti</th><th>PC assegnati</th><th>PC pagati</th><th>Stato</th><th>Azioni</th></tr></thead>
    <tbody>
    <?php foreach ($customers as $c) { ?>
      <tr>
        <td><?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?></td>
        <td><?php echo htmlspecialchars($c['email']); ?></td>
        <td><?php echo (int)($c['pc_requested_count'] ?? 0); ?></td>
        <td><?php echo (int)($c['laptops_count'] ?? 0); ?></td>
        <td><?php echo (int)($c['pcs_paid_total'] ?? 0); ?></td>
        <?php $req = (int)($c['pc_requested_count'] ?? 0); $ass = (int)($c['laptops_count'] ?? 0); $paid = (int)($c['pcs_paid_total'] ?? 0); ?>
        <td><span class="badge bg-<?php echo ($req === $ass && $ass === $paid) ? 'success' : 'danger'; ?>"><?php echo ($req === $ass && $ass === $paid) ? 'ok' : 'no'; ?></span></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/customers/'.$c['id']); ?>">Apri</a>
          <?php if (\App\Core\Auth::isAdmin()) { ?>
          <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal" data-id="<?php echo $c['id']; ?>" data-name="<?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?>">Elimina</button>
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
    $('#customersTable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
      order: [],
      columnDefs: [
        { targets: -1, orderable: false, searchable: false }
      ],
      dom: 'B<"d-flex justify-content-end align-items-center"f>rt<"d-flex justify-content-between align-items-center mt-2"l i p>',
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
    var t = document.getElementById('customersTable');
    var wid = t.id + '_search';
    var wrap = $(t).closest('.dataTables_wrapper');
    var lbl = wrap.find('.dataTables_filter label');
    var inp = lbl.find('input');
    inp.attr({ id: wid, name: wid, 'aria-label': 'Cerca docenti' });
    lbl.attr('for', wid);
    var lsel = wrap.find('.dataTables_length select');
    var lid = t.id + '_length';
    lsel.attr({ id: lid, name: lid, 'aria-label': 'Numero righe' });
  });
</script>

<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteCustomerForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di voler eliminare il docente <span id="delCustomerName"></span>?</p>
          <p class="text-danger small mb-0">Verranno eliminati anche tutti i pagamenti associati.</p>
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
  document.getElementById('deleteCustomerModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name');
    document.getElementById('deleteCustomerForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/customers/'); ?>' + id + '/delete');
    document.getElementById('delCustomerName').textContent = name || '';
  });
 </script>
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo \App\Core\Helpers::url('/customers/import'); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Importa Docenti da CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="customers_csv_file">Seleziona file CSV</label>
            <input type="file" id="customers_csv_file" name="csv_file" class="form-control" required accept=".csv">
          </div>
          <p class="small text-muted">Il file deve avere le colonne: first_name, last_name, email, notes, pc_requested_count. Opzionali: id, created_at, updated_at</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Importa</button>
        </div>
      </form>
    </div>
  </div>
</div>
