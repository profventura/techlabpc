<h3 class="mb-3">Software</h3>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/software/create'); ?>">Nuovo Software</a></div>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
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
