<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($laptop['code'].' - '.$laptop['brand_model']); ?></h5>
          <div>
            <a class="btn btn-warning" href="<?php echo \App\Core\Helpers::url('/laptops/'.$laptop['id'].'/edit'); ?>">Modifica</a>
            <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModalShow">Elimina</button>
            <a class="btn btn-outline-secondary ms-2" href="<?php echo \App\Core\Helpers::url('/laptops'); ?>">Torna alla lista</a>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <?php $__status_labels_top = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato','missing_office'=>'Manca Office']; ?>
            <p><strong>Stato:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($__status_labels_top[$laptop['status']] ?? $laptop['status']); ?></span></p>
            <p><strong>Licenza Office:</strong> <?php echo htmlspecialchars($laptop['office_license']??''); ?></p>
            <p><strong>Licenza Windows:</strong> <?php echo htmlspecialchars($laptop['windows_license']??''); ?></p>
          </div>
          <div class="col-md-6">
            <p><strong>Docente:</strong> <?php echo htmlspecialchars(trim(($laptop['customer_last_name']??'').' '.($laptop['customer_first_name']??''))); ?></p>
            <p><strong>Gruppo:</strong> <?php echo htmlspecialchars($laptop['group_name']??''); ?></p>
            <p><strong>Software richiesti:</strong></p>
            <ul class="mb-2">
              <?php foreach (($software_list??[]) as $s) { ?>
                <li><?php echo htmlspecialchars($s['name'].($s['version']?' '.$s['version']:'')); ?></li>
              <?php } ?>
              <?php if (empty($software_list)) { ?><li>Nessuno</li><?php } ?>
            </ul>
            <p><strong>Note software:</strong> <?php echo htmlspecialchars($laptop['other_software_request']??''); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModalShow" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteFormShow" action="<?php echo \App\Core\Helpers::url('/laptops/'.$laptop['id'].'/delete'); ?>" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di voler eliminare il PC (<?php echo htmlspecialchars($laptop['code']); ?>)?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-danger">Elimina</button>
        </div>
      </form>
    </div>
  </div>
 </div>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Storico stato</h5>
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead class="table-light"><tr><th>Data</th><th>Da</th><th>A</th><th>Operatore</th><th>Nota</th></tr></thead>
            <tbody>
            <?php $__status_labels = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_office'=>'Manca Office','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; foreach ($history as $h) { ?>
              <tr>
                <td><?php echo htmlspecialchars($h['created_at']); ?></td>
                <td><?php echo htmlspecialchars($__status_labels[$h['previous_status']] ?? $h['previous_status']); ?></td>
                <td><?php echo htmlspecialchars($__status_labels[$h['new_status']] ?? $h['new_status']); ?></td>
                <td><?php echo htmlspecialchars(trim(($h['first_name']??'').' '.($h['last_name']??''))); ?></td>
                <td><?php echo htmlspecialchars($h['note']??''); ?></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

