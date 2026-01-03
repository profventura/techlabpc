<!--
  Vista: logs/index.php
  Scopo: Mostra i log di accesso e di azione con tabelle DataTables, pulsanti di svuotamento e filtri.
-->
<h3 class="mb-3">Logs</h3>
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="d-flex justify-content-end mb-3 gap-2">
  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearAccessLogsModal">Svuota logs accessi</button>
  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearActionLogsModal">Svuota log Azioni</button>
</div>
<?php } ?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Accessi</h5>
        <div class="table-responsive">
          <table id="accessLogsTable" class="table table-striped table-bordered text-nowrap">
            <thead class="table-light"><tr><th>Data</th><th>Utente</th><th>Evento</th><th>IP</th></tr></thead>
            <tbody>
              <?php foreach ($access as $a) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($a['created_at']); ?></td>
                  <td><?php echo htmlspecialchars(trim(($a['first_name']??'').' '.($a['last_name']??''))); ?></td>
                  <td><?php echo htmlspecialchars($a['event']); ?></td>
                  <td><?php echo htmlspecialchars($a['ip']??''); ?></td>
                </tr>
              <?php } ?>
              <?php if (empty($access)) { ?><tr><td class="text-muted">Nessun log accessi</td><td></td><td></td><td></td></tr><?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Azioni</h5>
        <div class="table-responsive">
          <table id="actionLogsTable" class="table table-striped table-bordered text-nowrap">
            <thead class="table-light"><tr><th>Data</th><th>Utente</th><th>Azione</th><th>PC</th><th>Docente</th><th>Gruppo</th><th>Nota</th></tr></thead>
            <tbody>
              <?php foreach ($actions as $l) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($l['created_at']); ?></td>
                  <td><?php echo htmlspecialchars(trim(($l['first_name']??'').' '.($l['last_name']??''))); ?></td>
                  <td><?php echo htmlspecialchars($l['action_type']); ?></td>
                  <td><?php echo htmlspecialchars($l['laptop_code']??''); ?></td>
                  <td><?php echo htmlspecialchars(trim(($l['customer_last_name']??'').' '.($l['customer_first_name']??''))); ?></td>
                  <td><?php echo htmlspecialchars($l['group_name']??''); ?></td>
                  <td><?php echo htmlspecialchars($l['note']??''); ?></td>
                </tr>
              <?php } ?>
              <?php if (empty($actions)) { ?><tr><td class="text-muted">Nessun log azioni</td><td></td><td></td><td></td><td></td><td></td><td></td></tr><?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    if (!window.jQuery) return;
    var $ = window.jQuery;
    // Inizializzazione DataTable per la tabella Accessi
    $('#accessLogsTable').DataTable({
      responsive: true,
      deferRender: true,
      autoWidth: false,
      pageLength: <?php echo (isset($len_access) && (int)$len_access !== 0 ? (int)$len_access : 10); ?>,
      lengthMenu: [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
      order: [],
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
    var t1 = document.getElementById('accessLogsTable');
    var wid1 = t1.id + '_search';
    var wrap1 = $(t1).closest('.dataTables_wrapper');
    var lbl1 = wrap1.find('.dataTables_filter label');
    var inp1 = lbl1.find('input');
    inp1.attr({ id: wid1, name: wid1, 'aria-label': 'Cerca accessi' });
    lbl1.attr('for', wid1);
    var lsel1 = wrap1.find('.dataTables_length select');
    var lid1 = t1.id + '_length';
    lsel1.attr({ id: lid1, name: lid1, 'aria-label': 'Numero righe' });
    // Inizializzazione DataTable per la tabella Azioni
    $('#actionLogsTable').DataTable({
      responsive: true,
      deferRender: true,
      autoWidth: false,
      pageLength: <?php echo (isset($len_actions) && (int)$len_actions !== 0 ? (int)$len_actions : 10); ?>,
      lengthMenu: [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
      order: [],
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
    var t2 = document.getElementById('actionLogsTable');
    var wid2 = t2.id + '_search';
    var wrap2 = $(t2).closest('.dataTables_wrapper');
    var lbl2 = wrap2.find('.dataTables_filter label');
    var inp2 = lbl2.find('input');
    inp2.attr({ id: wid2, name: wid2, 'aria-label': 'Cerca azioni' });
    lbl2.attr('for', wid2);
    var lsel2 = wrap2.find('.dataTables_length select');
    var lid2 = t2.id + '_length';
    lsel2.attr({ id: lid2, name: lid2, 'aria-label': 'Numero righe' });
  });
</script>
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="modal fade" id="clearAccessLogsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo \App\Core\Helpers::url('/logs/clear-access'); ?>" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma svuotamento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Svuotare tutti i logs di accesso?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-danger">Svuota</button>
        </div>
      </form>
    </div>
  </div>
 </div>
<div class="modal fade" id="clearActionLogsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo \App\Core\Helpers::url('/logs/clear-actions'); ?>" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma svuotamento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Svuotare tutti i log azioni?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-danger">Svuota</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>

