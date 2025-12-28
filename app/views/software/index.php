<h3 class="mb-3">Software</h3>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/software/create'); ?>">Nuovo Software</a></div>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Nome</th><th>Versione</th><th>Licenza</th><th>Costo</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $s) { ?>
      <tr>
        <td><?php echo htmlspecialchars($s['name']); ?></td>
        <td><?php echo htmlspecialchars($s['version']); ?></td>
        <td><?php echo htmlspecialchars($s['license']); ?></td>
        <td><?php echo $s['cost']!==null ? number_format((float)$s['cost'], 2, ',', '.') : ''; ?></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/software/'.$s['id'].'/edit'); ?>">Modifica</a>
          <form method="post" action="<?php echo \App\Core\Helpers::url('/software/'.$s['id'].'/delete'); ?>" class="d-inline">
            <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
          </form>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <?php if (empty($items)) { ?><div class="alert alert-info">Nessun software presente.</div><?php } ?>
</div>
