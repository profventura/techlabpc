<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($customer['last_name'].' '.$customer['first_name']); ?></h5>
            <a href="<?php echo \App\Core\Helpers::url('/customers/'.$customer['id'].'/edit'); ?>" class="btn btn-warning">Modifica</a>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>PC richiesti:</strong> <?php echo (int)($customer['pc_requested_count'] ?? 0); ?></p>
                <p><strong>PC pagati:</strong> <?php $pcp=0; foreach ($payments as $p) { if (($p['status']??'')==='verified') { $pcp += (int)($p['pcs_paid_count']??0); } } echo $pcp; ?></p>
                <p><strong>Note:</strong> <?php echo htmlspecialchars($customer['notes']); ?></p>
            </div>
        </div>

        <h5 class="card-title fw-semibold mb-3">PC assegnati</h5>
        <p class="mb-2"><strong>Totale PC:</strong> <?php echo count($laptops); ?></p>
        <div class="table-responsive mb-4">
          <table class="table table-striped table-bordered text-nowrap">
            <thead class="table-light"><tr><th>Codice</th><th>Modello</th><th>Stato</th><th>Gruppo</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($laptops as $l) { ?>
              <tr>
                <td><?php echo htmlspecialchars($l['code']); ?></td>
                <td><?php echo htmlspecialchars($l['brand_model']); ?></td>
                <?php $__status_labels_row = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; ?>
                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($__status_labels_row[$l['status']] ?? $l['status']); ?></span></td>
                <td><?php echo htmlspecialchars($l['group_id'] ? $l['group_id'] : ''); ?></td>
                <td><a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/laptops/'.$l['id']); ?>">Apri</a></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>

        <h5 class="card-title fw-semibold mb-3">Pagamenti bonifico</h5>
        <div class="table-responsive">
          <table class="table table-striped table-bordered text-nowrap">
            <thead class="table-light"><tr><th>Data</th><th>Importo</th><th>PC pagati</th><th>Stato</th><th>Contabile</th><th>Riferimento</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p) { ?>
              <tr>
                <td><?php echo htmlspecialchars($p['paid_at']); ?></td>
                <td>€<?php echo number_format($p['amount'],2,',','.'); ?></td>
                <td><?php echo (int)($p['pcs_paid_count'] ?? 0); ?></td>
                <?php $__pay_labels = ['pending'=>'In attesa','verified'=>'Verificato','rejected'=>'Rifiutato']; $st = $p['status']; ?>
                <td><span class="badge bg-<?php echo $st=='verified'?'success':($st=='rejected'?'danger':'warning'); ?>"><?php echo htmlspecialchars($__pay_labels[$st] ?? $st); ?></span></td>
                <td><?php echo $p['receipt_path'] ? '<a class="btn btn-sm btn-outline-info" href="'.\App\Core\Helpers::url($p['receipt_path']).'" target="_blank">Apri</a>' : ''; ?></td>
                <td><?php echo htmlspecialchars($p['reference']??''); ?></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
