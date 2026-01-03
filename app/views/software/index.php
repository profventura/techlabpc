<!--
  Vista: software/index.php
  Scopo: Elenco del catalogo software con card riepilogative e gestione CRUD.
-->
<h3 class="mb-3">Software</h3>
<div class="row mb-3">
  <div class="col-md-4">
    <div class="card bg-primary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:inbox-in-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero software</h5>
        <h2 class="card-text text-primary text-center metric-value" data-metric="total"><?php echo (int)($summary['total'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-success-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-success flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:tag-price-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Software free</h5>
        <h2 class="card-text text-success text-center metric-value" data-metric="free"><?php echo (int)($summary['free'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-info-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-info flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:dollar-minimalistic-linear" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Software a pagamento</h5>
        <h2 class="card-text text-info text-center metric-value" data-metric="paid"><?php echo (int)($summary['paid'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
</div>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/software/create'); ?>">Nuovo Software</a></div>
<div class="table-responsive">
  <table id="softwareTable" class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Nome</th><th>Versione</th><th>Licenza</th><th>Costo</th><th>Elimina</th></tr></thead>
    <tbody>
    <?php foreach ($items as $s) { ?>
      <tr>
        <td><?php echo htmlspecialchars($s['name']); ?></td>
        <td><?php echo htmlspecialchars($s['version']); ?></td>
        <td><?php echo htmlspecialchars($s['license']); ?></td>
        <td><?php echo $s['cost']!==null ? number_format((float)$s['cost'], 2, ',', '.') : ''; ?></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/software/'.$s['id'].'/edit'); ?>">Modifica</a>
          <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteSoftwareModal" data-id="<?php echo $s['id']; ?>" data-name="<?php echo htmlspecialchars($s['name'].' '.($s['version']??'')); ?>">Elimina</button>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <?php if (empty($items)) { ?><div class="alert alert-info">Nessun software presente.</div><?php } ?>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    if (!window.jQuery) return;
    var $ = window.jQuery;
    // Inizializzazione DataTable per il catalogo software
    $('#softwareTable').DataTable({
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
        paginate: { first: 'Prima', last: 'Ultima', next: 'Successiva', previous: 'Precedente' }
      }
    });
    var t = document.getElementById('softwareTable');
    var wid = t.id + '_search';
    var wrap = $(t).closest('.dataTables_wrapper');
    var lbl = wrap.find('.dataTables_filter label');
    var inp = lbl.find('input');
    inp.attr({ id: wid, name: wid, 'aria-label': 'Cerca software' });
    lbl.attr('for', wid);
    var lsel = wrap.find('.dataTables_length select');
    var lid = t.id + '_length';
    lsel.attr({ id: lid, name: lid, 'aria-label': 'Numero righe' });
    // Aggiornamento asincrono dei valori card (scope software)
    fetch('<?php echo \App\Core\Helpers::url('/api/view-cards'); ?>?scope=software').then(function(r){ return r.json(); }).then(function(data){
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

<div class="modal fade" id="deleteSoftwareModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteSoftwareForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione software</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di voler eliminare <span id="delSoftwareName"></span>?</p>
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
  document.getElementById('deleteSoftwareModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name');
    document.getElementById('deleteSoftwareForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/software/'); ?>' + id + '/delete');
    document.getElementById('delSoftwareName').textContent = name || '';
  });
</script>
