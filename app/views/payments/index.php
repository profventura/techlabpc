<h3 class="mb-3">Pagamenti bonifico</h3>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/payments/create'); ?>">Nuovo Bonifico</a></div>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Docente</th><th>Data</th><th>Importo</th><th>Stato</th><th>Contabile</th></tr></thead>
    <tbody>
    <?php $__pay_labels = ['pending'=>'In attesa','verified'=>'Verificato','rejected'=>'Rifiutato']; foreach ($payments as $p) { ?>
      <tr>
        <td><?php echo htmlspecialchars($p['last_name'].' '.$p['first_name']); ?></td>
        <td><?php echo htmlspecialchars($p['paid_at']); ?></td>
        <td>€<?php echo number_format($p['amount'],2,',','.'); ?></td>
        <?php $st = $p['status']; ?>
        <td><span class="badge bg-<?php echo $st=='verified'?'success':($st=='rejected'?'danger':'warning'); ?>"><?php echo htmlspecialchars($__pay_labels[$st] ?? $st); ?></span></td>
        <td>
          <a class="btn btn-sm btn-outline-primary <?php echo $p['receipt_path']?'':'disabled'; ?>" href="<?php echo $p['receipt_path'] ? \App\Core\Helpers::url($p['receipt_path']) : '#'; ?>" target="_blank">Apri</a>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

