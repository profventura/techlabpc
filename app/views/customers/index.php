<h3 class="mb-3">Docenti</h3>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/customers/create'); ?>">Nuovo Docente</a></div>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Nome</th><th>Email</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($customers as $c) { ?>
      <tr>
        <td><?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?></td>
        <td><?php echo htmlspecialchars($c['email']); ?></td>
        <td><a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/customers/'.$c['id']); ?>">Apri</a></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

