<h3 class="mb-3">Gruppi</h3>
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="d-flex justify-content-end mb-3 gap-2">
  <a class="btn btn-outline-success export-csv" data-count="<?php echo count($groups); ?>" href="<?php echo \App\Core\Helpers::url('/work-groups/export'); ?>">Export CSV</a>
  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importWgModal">Import CSV</button>
  <a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/work-groups/create'); ?>">Nuovo Gruppo</a>
</div>
<?php } ?>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Nome</th><th>Responsabile</th><th>Azioni</th></tr></thead>
    <tbody>
    <?php foreach ($groups as $g) { ?>
      <tr>
        <td><?php echo htmlspecialchars($g['name']); ?></td>
        <td><?php echo htmlspecialchars($g['leader_last_name'].' '.$g['leader_first_name']); ?></td>
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
            <label class="form-label">Seleziona file CSV</label>
            <input type="file" name="csv_file" class="form-control" required accept=".csv">
          </div>
          <p class="small text-muted">Il file deve avere le colonne: name, leader_student_id</p>
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

