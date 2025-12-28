<h3 class="mb-3">Logs</h3>
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Accessi</h5>
        <div class="table-responsive">
          <table class="table table-striped table-bordered text-nowrap">
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
              <?php if (empty($access)) { ?><tr><td colspan="4">Nessun log accessi</td></tr><?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Azioni</h5>
        <div class="table-responsive">
          <table class="table table-striped table-bordered text-nowrap">
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
              <?php if (empty($actions)) { ?><tr><td colspan="7">Nessun log azioni</td></tr><?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
