<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($group['name']); ?></h5>
            <?php if (\App\Core\Auth::isAdmin()) { ?>
              <a href="<?php echo \App\Core\Helpers::url('/work-groups/'.$group['id'].'/edit'); ?>" class="btn btn-warning">Modifica</a>
            <?php } ?>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Responsabile:</strong> <?php echo htmlspecialchars($group['leader_last_name'].' '.$group['leader_first_name']); ?></p>
            </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <h5 class="card-title fw-semibold mb-3">Membri</h5>
            <div class="table-responsive mb-4">
              <table class="table table-striped table-bordered text-nowrap">
                <thead class="table-light"><tr><th>Nome</th><th>Ruolo</th></tr></thead>
                <tbody>
                <?php foreach ($members as $m) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($m['last_name'].' '.$m['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($m['role']); ?></td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>

            <?php if (\App\Core\Auth::isAdmin()) { ?>
            <div class="card bg-light">
              <div class="card-body">
                <h6 class="card-title fw-semibold mb-3">Aggiungi membro</h6>
                <form method="post" action="<?php echo \App\Core\Helpers::url('/work-groups/'.$group['id'].'/add-member'); ?>">
                  <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
                  <div class="row">
                    <div class="col-md-6 mb-2">
                        <select class="form-select" name="student_id">
                          <?php foreach ((new \App\Models\Student())->all() as $s) { ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['last_name'].' '.$s['first_name']); ?></option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <select class="form-select" name="role">
                          <option value="installer">installer</option>
                          <option value="leader">leader</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary w-100 text-nowrap">Aggiungi</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <?php } ?>
          </div>

          <div class="col-md-6">
            <h5 class="card-title fw-semibold mb-3">PC in carico</h5>
            <div class="table-responsive">
              <table class="table table-striped table-bordered text-nowrap">
                <thead class="table-light"><tr><th>Codice</th><th>Modello</th><th>Stato</th></tr></thead>
                <tbody>
                <?php $__status_labels_row = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; foreach ($laptops as $l) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($l['code']); ?></td>
                    <td><?php echo htmlspecialchars($l['brand_model']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($__status_labels_row[$l['status']] ?? $l['status']); ?></span></td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
