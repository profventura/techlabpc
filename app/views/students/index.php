<h3 class="mb-3">Studenti</h3>
<div class="row mb-3">
  <div class="col-md-4">
    <div class="card bg-primary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:user-circle-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero studenti</h5>
        <h2 class="card-text text-primary text-center"><?php echo (int)($summary['students'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-success-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-success flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:check-circle-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero responsabili</h5>
        <h2 class="card-text text-success text-center"><?php echo (int)($summary['leaders'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-warning-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-warning flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:settings-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero installatori</h5>
        <h2 class="card-text text-warning text-center"><?php echo (int)($summary['installers'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
</div>
<div class="d-flex justify-content-end mb-3 gap-2">
  <a class="btn btn-outline-success export-csv" data-count="<?php echo count($students); ?>" href="<?php echo \App\Core\Helpers::url('/students/export'); ?>">Esporta CSV</a>
  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">Importa CSV</button>
  <a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/students/create'); ?>">Nuovo Studente</a>
</div>
<form method="get" class="row g-2 align-items-center mb-3">
  <div class="col-auto">
    <select name="role" class="form-select">
      <option value="">Ruolo</option>
      <option value="admin" <?php echo ($filters['role']??'')==='admin'?'selected':''; ?>>Admin</option>
      <option value="student" <?php echo ($filters['role']??'')==='student'?'selected':''; ?>>Student</option>
    </select>
  </div>
  <div class="col-auto">
    <select name="active" class="form-select">
      <option value="">Attivo</option>
      <option value="1" <?php echo ($filters['active']??'')==='1'?'selected':''; ?>>Sì</option>
      <option value="0" <?php echo ($filters['active']??'')==='0'?'selected':''; ?>>No</option>
    </select>
  </div>
  <div class="col-auto">
    <select name="group_id" class="form-select">
      <option value="">Gruppo</option>
      <?php foreach (($groups??[]) as $g) { ?>
        <option value="<?php echo $g['id']; ?>" <?php echo ($filters['group_id']??'')==$g['id']?'selected':''; ?>><?php echo htmlspecialchars($g['name']); ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-auto">
    <input type="text" name="q" class="form-control" value="<?php echo htmlspecialchars($filters['q']??''); ?>" placeholder="Nome/Email">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-outline-primary">Filtra</button>
  </div>
</form>
<div class="table-responsive">
  <table id="studentsTable" class="table table-striped table-bordered text-nowrap">
    <thead class="table-light">
      <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Gruppo</th>
        <th>Ruolo</th>
        <th>Attivo</th>
        <th>Azioni</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($students as $s) { ?>
        <tr>
          <td><?php echo htmlspecialchars($s['last_name'] . ' ' . $s['first_name']); ?></td>
          <td><?php echo htmlspecialchars($s['email']); ?></td>
          <td><?php echo htmlspecialchars($s['group_names'] ?? ''); ?></td>
          <td>
            <?php if (!empty($s['group_roles'])) { ?>
              <span class="badge bg-secondary"><?php echo htmlspecialchars($s['group_roles']); ?></span>
            <?php } else { ?>
              <span class="badge bg-secondary"><?php echo htmlspecialchars($s['role']); ?></span>
            <?php } ?>
          </td>
          <td>
            <?php echo $s['active'] ? '<span class="badge bg-success">Sì</span>' : '<span class="badge bg-secondary">No</span>'; ?>
          </td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/students/' . $s['id'] . '/edit'); ?>">Modifica</a>
            <?php if (\App\Core\Auth::isAdmin()) { ?>
            <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteStudentModal" data-id="<?php echo $s['id']; ?>" data-name="<?php echo htmlspecialchars($s['last_name'].' '.$s['first_name']); ?>">Elimina</button>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    var t = document.getElementById('studentsTable');
    if (!t || !window.jQuery) return;
    var $ = window.jQuery;
    var dt = $(t).DataTable({
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
    var wid = t.id + '_search';
    var wrap = $(t).closest('.dataTables_wrapper');
    var lbl = wrap.find('.dataTables_filter label');
    var inp = lbl.find('input');
    inp.attr({ id: wid, name: wid, 'aria-label': 'Cerca studenti' });
    lbl.attr('for', wid);
    var lsel = wrap.find('.dataTables_length select');
    var lid = t.id + '_length';
    lsel.attr({ id: lid, name: lid, 'aria-label': 'Numero righe' });
  });
  </script>
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteStudentForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di voler eliminare lo studente <span id="delStudentName"></span>?</p>
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
  document.getElementById('deleteStudentModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name');
    document.getElementById('deleteStudentForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/students/'); ?>' + id + '/delete');
    document.getElementById('delStudentName').textContent = name || '';
  });
</script>
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo \App\Core\Helpers::url('/students/import'); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Importa Studenti da CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="students_csv_file">Seleziona file CSV</label>
            <input type="file" id="students_csv_file" name="csv_file" class="form-control" required accept=".csv">
          </div>
          <p class="small text-muted">Il file deve avere le colonne: first_name, last_name, email, role, active. Opzionali: id, password, password_hash, created_at, updated_at. Se password non è presente, default: 12345678</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Importa</button>
        </div>
      </form>
    </div>
  </div>
</div>
