<h3 class="mb-3">Studenti</h3>
<div class="d-flex justify-content-end mb-3 gap-2">
  <a class="btn btn-outline-success export-csv" data-count="<?php echo count($students); ?>" href="<?php echo \App\Core\Helpers::url('/students/export'); ?>">Export CSV</a>
  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">Import CSV</button>
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
            <label class="form-label">Seleziona file CSV</label>
            <input type="file" name="csv_file" class="form-control" required accept=".csv">
          </div>
          <p class="small text-muted">Il file deve avere le colonne: first_name, last_name, email, role, active. Password default: 12345678</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Importa</button>
        </div>
      </form>
    </div>
  </div>
</div>
