<h3 class="mb-3">Gruppi</h3>
<?php if (\App\Core\Auth::isAdmin()) { ?><div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/work-groups/create'); ?>">Nuovo Gruppo</a></div><?php } ?>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Nome</th><th>Responsabile</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($groups as $g) { ?>
      <tr>
        <td><?php echo htmlspecialchars($g['name']); ?></td>
        <td><?php echo htmlspecialchars($g['leader_last_name'].' '.$g['leader_first_name']); ?></td>
        <td><a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/work-groups/'.$g['id']); ?>">Apri</a></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

