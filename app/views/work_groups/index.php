<!--
  Vista: work_groups/index.php
  Scopo: Elenco gruppi di lavoro con card riepilogative e azioni di gestione (solo admin per create/delete).
-->
<h3 class="mb-3">Gruppi</h3>
<div class="row mb-3">
  <div class="col-md-4">
    <div class="card bg-info-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-info flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:users-group-two-rounded-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero gruppi</h5>
        <h2 class="card-text text-info text-center metric-value" data-metric="groups"><?php echo (int)($summary['groups'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-primary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:user-circle-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero studenti</h5>
        <h2 class="card-text text-primary text-center metric-value" data-metric="students"><?php echo (int)($summary['students'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-secondary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:laptop-minimalistic-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero PC assegnati</h5>
        <h2 class="card-text text-secondary text-center metric-value" data-metric="laptops"><?php echo (int)($summary['laptops'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
</div>
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="d-flex justify-content-end mb-3 gap-2">
  <a class="btn btn-outline-success export-csv" data-count="<?php echo count($groups); ?>" href="<?php echo \App\Core\Helpers::url('/work-groups/export'); ?>">Esporta CSV</a>
  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importWgModal">Importa CSV</button>
  <a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/work-groups/create'); ?>">Nuovo Gruppo</a>
</div>
<?php } ?>
<div class="table-responsive">
  <table id="workGroupsTable" class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Nome</th><th>Responsabile</th><th>Numero studenti</th><th>Numero PC</th><th>Azioni</th></tr></thead>
    <tbody>
    <?php foreach ($groups as $g) { ?>
      <tr>
        <td><?php echo htmlspecialchars($g['name']); ?></td>
        <td><?php echo htmlspecialchars($g['leader_last_name'].' '.$g['leader_first_name']); ?></td>
        <td><?php echo (int)($g['members_count'] ?? 0); ?></td>
        <td><?php echo (int)($g['laptops_count'] ?? 0); ?></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/work-groups/'.$g['id']); ?>">Apri</a>
          <?php if (\App\Core\Auth::isAdmin()) { ?>
          <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteGroupModal" data-id="<?php echo $g['id']; ?>" data-name="<?php echo htmlspecialchars($g['name']); ?>">Elimina</button>
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
    // Inizializzazione DataTable per la lista gruppi
    $('#workGroupsTable').DataTable({
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
    var t = document.getElementById('workGroupsTable');
    var wid = t.id + '_search';
    var wrap = $(t).closest('.dataTables_wrapper');
    var lbl = wrap.find('.dataTables_filter label');
    var inp = lbl.find('input');
    inp.attr({ id: wid, name: wid, 'aria-label': 'Cerca gruppi' });
    lbl.attr('for', wid);
    var lsel = wrap.find('.dataTables_length select');
    var lid = t.id + '_length';
    lsel.attr({ id: lid, name: lid, 'aria-label': 'Numero righe' });
    // Aggiornamento asincrono delle card (scope groups)
    fetch('<?php echo \App\Core\Helpers::url('/api/view-cards'); ?>?scope=groups').then(function(r){ return r.json(); }).then(function(data){
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
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="modal fade" id="importWgModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo \App\Core\Helpers::url('/work-groups/import'); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Importa Gruppi da CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="work_groups_csv_file">Seleziona file CSV</label>
            <input type="file" id="work_groups_csv_file" name="csv_file" class="form-control" required accept=".csv">
          </div>
          <p class="small text-muted">Il file deve avere le colonne: name, leader_student_id. Opzionali: id, created_at, updated_at</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Importa</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteGroupForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di voler eliminare il gruppo <span id="delGroupName"></span>?</p>
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
  document.getElementById('deleteGroupModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name');
    document.getElementById('deleteGroupForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/work-groups/'); ?>' + id + '/delete');
    document.getElementById('delGroupName').textContent = name || '';
  });
</script>
