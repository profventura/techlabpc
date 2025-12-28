<h3 class="mb-3">PC</h3>
<div class="d-flex justify-content-end mb-3">
  <a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/laptops/create'); ?>">Nuovo PC</a>
</div>
<form method="get" class="row g-2 align-items-center mb-3">
  <div class="col-auto">
    <?php $__status_labels = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; ?>
    <select name="status" class="form-select">
      <option value="">Stato</option>
      <?php foreach ($__status_labels as $val=>$label) { ?>
        <option value="<?php echo $val; ?>" <?php echo ($filters['status']??'')===$val?'selected':''; ?>><?php echo $label; ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-auto">
    <select name="customer_id" class="form-select">
      <option value="">Docente</option>
      <?php foreach ($customers as $c) { ?>
        <option value="<?php echo $c['id']; ?>" <?php echo ($filters['customer_id']??'')==$c['id']?'selected':''; ?>><?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-auto">
    <select name="group_id" class="form-select">
      <option value="">Gruppo</option>
      <?php foreach ($groups as $g) { ?>
        <option value="<?php echo $g['id']; ?>" <?php echo ($filters['group_id']??'')==$g['id']?'selected':''; ?>><?php echo htmlspecialchars($g['name']); ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-outline-primary">Filtra</button>
  </div>
</form>
<div class="table-responsive">
  <table class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Codice</th><th>Modello</th><th>Stato</th><th>Docente</th><th>Gruppo</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($laptops as $l) { ?>
      <tr>
        <td><?php echo htmlspecialchars($l['code']); ?></td>
        <td><?php echo htmlspecialchars($l['brand_model']); ?></td>
        <?php $__status_labels_row = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; ?>
        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($__status_labels_row[$l['status']] ?? $l['status']); ?></span></td>
        <td><?php echo htmlspecialchars(trim(($l['customer_last_name']??'').' '.($l['customer_first_name']??''))); ?></td>
        <td><?php echo htmlspecialchars($l['group_name']??''); ?></td>
        <td><a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/laptops/'.$l['id']); ?>">Apri</a></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

