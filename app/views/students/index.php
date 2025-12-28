<h3 class="mb-3">Studenti</h3>
<div class="d-flex justify-content-end mb-3">
  <a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/students/create'); ?>">Nuovo Studente</a>
</div>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light">
      <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Ruolo</th>
        <th>Attivo</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($students as $s) { ?>
        <tr>
          <td><?php echo htmlspecialchars($s['last_name'] . ' ' . $s['first_name']); ?></td>
          <td><?php echo htmlspecialchars($s['email']); ?></td>
          <td>
            <span class="badge bg-<?php echo $s['role'] == 'admin' ? 'danger' : 'primary'; ?>">
              <?php echo htmlspecialchars($s['role']); ?>
            </span>
          </td>
          <td>
            <?php echo $s['active'] ? '<span class="badge bg-success">Sì</span>' : '<span class="badge bg-secondary">No</span>'; ?>
          </td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/students/' . $s['id'] . '/edit'); ?>">Modifica</a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>