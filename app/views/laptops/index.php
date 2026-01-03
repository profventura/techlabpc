<!--
  Vista: laptops/index.php
  Scopo: Elenco dei PC/Laptop con filtri, card riepilogative e funzionalità import/export.
-->
<h3 class="mb-3">Computer</h3>
<div class="row mb-3">
  <div class="col-md-4">
    <div class="card bg-primary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:laptop-minimalistic-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero computer</h5>
        <h2 class="card-text text-primary text-center metric-value" data-metric="total"><?php echo (int)($summary['total'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-success-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-success flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:check-circle-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Computer Pronti</h5>
        <h2 class="card-text text-success text-center metric-value" data-metric="ready"><?php echo (int)($summary['ready'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-warning-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-warning flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:settings-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Lavorazione in corso</h5>
        <h2 class="card-text text-warning text-center metric-value" data-metric="in_work"><?php echo (int)($summary['in_work'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
</div>
<div class="d-flex justify-content-end mb-3 gap-2">
  <a class="btn btn-outline-success export-csv" data-count="<?php echo count($laptops); ?>" href="<?php echo \App\Core\Helpers::url('/laptops/export'); ?>">Esporta CSV</a>
  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">Importa CSV</button>
  <a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/laptops/create'); ?>">Nuovo Computer</a>
</div>
<form method="get" class="row g-2 align-items-center mb-3">
  <div class="col-auto">
    <?php $__status_labels = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; ?>
    <select name="status" class="form-select">
      <option value="">Stato</option>
      <?php foreach ($__status_labels as $val=>$label) { ?>
        <option value="<?php echo $val; ?>" <?php echo ($filters['status']??'')===$val?'selected':''; ?>><?php echo $label; ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-auto">
    <select name="customer_id" class="form-select">
      <option value="">Docente</option>
      <?php foreach ($customers as $c) { ?>
        <option value="<?php echo $c['id']; ?>" <?php echo ($filters['customer_id']??'')==$c['id']?'selected':''; ?>><?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-auto">
    <select name="group_id" class="form-select">
      <option value="">Gruppo</option>
      <?php foreach ($groups as $g) { ?>
        <option value="<?php echo $g['id']; ?>" <?php echo ($filters['group_id']??'')==$g['id']?'selected':''; ?>><?php echo htmlspecialchars($g['name']); ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-outline-primary">Filtra</button>
  </div>
</form>
<div class="table-responsive">
  <table id="laptopsTable" class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Codice</th><th>Modello</th><th>Stato</th><th>Docente</th><th>Gruppo</th><th>Azioni</th></tr></thead>
    <tbody>
    <?php foreach ($laptops as $l) { ?>
      <tr>
        <td><?php echo htmlspecialchars($l['code']); ?></td>
        <td><?php echo htmlspecialchars($l['brand_model']); ?></td>
        <?php $__status_labels_row = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; ?>
        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($__status_labels_row[$l['status']] ?? $l['status']); ?></span></td>
        <td><?php echo htmlspecialchars(trim(($l['customer_last_name']??'').' '.($l['customer_first_name']??''))); ?></td>
        <td><?php echo htmlspecialchars($l['group_name']??''); ?></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/laptops/'.$l['id']); ?>">Apri</a>
          <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $l['id']; ?>" data-code="<?php echo htmlspecialchars($l['code']); ?>">Elimina</button>
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
    // Inizializzazione DataTable per i laptop con impostazioni performance
    $('#laptopsTable').DataTable({
      responsive: true,
      deferRender: true,
      autoWidth: false,
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
        paginate: {
          first: 'Prima',
          last: 'Ultima',
          next: 'Successiva',
          previous: 'Precedente'
        }
      }
    });
    var t = document.getElementById('laptopsTable');
    var wid = t.id + '_search';
    var wrap = $(t).closest('.dataTables_wrapper');
    var lbl = wrap.find('.dataTables_filter label');
    var inp = lbl.find('input');
    inp.attr({ id: wid, name: wid, 'aria-label': 'Cerca Computer' });
    lbl.attr('for', wid);
    var lsel = wrap.find('.dataTables_length select');
    var lid = t.id + '_length';
    lsel.attr({ id: lid, name: lid, 'aria-label': 'Numero righe' });
    // Aggiornamento asincrono dei valori delle card dal backend (view_cards)
    fetch('<?php echo \App\Core\Helpers::url('/api/view-cards'); ?>?scope=laptops').then(function(r){ return r.json(); }).then(function(data){
      if (data && data.metrics) {
        var ms = document.querySelectorAll('.metric-value');
        ms.forEach(function(el){
          var m = el.getAttribute('data-metric');
          if (m && Object.prototype.hasOwnProperty.call(data.metrics, m)) { el.textContent = parseInt(data.metrics[m], 10) || 0; }
        });
      }
    }).catch(function(){});
  });
</script>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo \App\Core\Helpers::url('/laptops/import'); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Importa Computer da CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="laptops_csv_file">Seleziona file CSV</label>
            <input type="file" id="laptops_csv_file" name="csv_file" class="form-control" required accept=".csv">
          </div>
          <p class="small text-muted">Il file deve avere le colonne: code, brand_model, cpu, ram, storage, screen, tech_notes, scratches, physical_condition, battery, condition_level, office_license, windows_license, other_software_request, status, customer_id, group_id, last_operator_student_id. Opzionali: id, created_at, updated_at</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Importa</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di voler eliminare il PC <span id="delCode"></span>?</p>
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
  document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var code = btn.getAttribute('data-code');
    document.getElementById('deleteForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/laptops/'); ?>' + id + '/delete');
    document.getElementById('delCode').textContent = code ? '(' + code + ')' : '';
  });
</script>
