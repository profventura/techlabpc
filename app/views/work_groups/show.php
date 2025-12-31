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
                <thead class="table-light"><tr><th>Nome</th><th>Ruolo</th><?php if (\App\Core\Auth::isAdmin()) { ?><th>Azioni</th><?php } ?></tr></thead>
                <tbody>
                <?php foreach ($members as $m) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($m['last_name'].' '.$m['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($m['role']); ?></td>
                    <?php if (\App\Core\Auth::isAdmin()) { ?>
                    <td>
                      <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#removeMemberModal" data-student="<?php echo $m['student_id']; ?>" data-name="<?php echo htmlspecialchars($m['last_name'].' '.$m['first_name']); ?>">Elimina</button>
                    </td>
                    <?php } ?>
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
                        <select class="form-select" name="student_id" id="addMemberStudent">
                          <?php foreach ((new \App\Models\Student())->withoutGroup() as $s) { ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['last_name'].' '.$s['first_name']); ?></option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <select class="form-select" name="role" id="addMemberRole">
                          <option value="installer">installer</option>
                          <option value="leader">leader</option>
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary w-100 text-nowrap" id="addMemberSubmit">Aggiungi</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>

          <div class="col-md-6">
            <h5 class="card-title fw-semibold mb-3">PC in carico</h5>
            <div class="table-responsive">
              <table class="table table-striped table-bordered text-nowrap">
                <thead class="table-light"><tr><th>Codice</th><th>Modello</th><th>Stato</th><?php if (\App\Core\Auth::isAdmin()) { ?><th>Azioni</th><?php } ?></tr></thead>
                <tbody>
                <?php $__status_labels_row = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; foreach ($laptops as $l) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($l['code']); ?></td>
                    <td><?php echo htmlspecialchars($l['brand_model']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($__status_labels_row[$l['status']] ?? $l['status']); ?></span></td>
                    <?php if (\App\Core\Auth::isAdmin()) { ?>
                    <td>
                      <a class="btn btn-sm btn-outline-primary" href="<?php echo \App\Core\Helpers::url('/laptops/'.$l['id']); ?>">Apri</a>
                      <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteLaptopModal" data-id="<?php echo $l['id']; ?>" data-code="<?php echo htmlspecialchars($l['code']); ?>">Elimina</button>
                    </td>
                    <?php } ?>
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
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="modal fade" id="removeMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="removeMemberForm" action="<?php echo \App\Core\Helpers::url('/work-groups/'.$group['id'].'/remove-member'); ?>" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <input type="hidden" name="student_id" id="removeMemberStudent">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione membro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Eliminare il membro <span id="removeMemberName"></span>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-danger">Elimina</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="confirmLeaderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Conferma cambio leader</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Impostare un nuovo leader sostituirà l’attuale leader e lo renderà installer. Confermi?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary" id="confirmLeaderBtn">Conferma</button>
      </div>
    </div>
  </div>
</div>
<script>
  document.getElementById('removeMemberModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var sid = btn.getAttribute('data-student');
    var name = btn.getAttribute('data-name');
    document.getElementById('removeMemberStudent').value = sid || '';
    document.getElementById('removeMemberName').textContent = name || '';
  });
  document.getElementById('deleteLaptopModal')?.addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var code = btn.getAttribute('data-code');
    document.getElementById('deleteLaptopForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/laptops/'); ?>' + id + '/delete');
    document.getElementById('delLaptopCode').textContent = code ? '(' + code + ')' : '';
  });
  document.getElementById('addMemberSubmit').addEventListener('click', function (ev) {
    var roleSel = document.getElementById('addMemberRole');
    var studentSel = document.getElementById('addMemberStudent');
    if (roleSel && roleSel.value === 'leader') {
      ev.preventDefault();
      var m = new bootstrap.Modal(document.getElementById('confirmLeaderModal'));
      m.show();
      document.getElementById('confirmLeaderBtn').onclick = function(){
        m.hide();
        roleSel.closest('form').submit();
      };
    }
    if (roleSel && roleSel.value === 'installer') {
      var currentLeaderId = <?php echo (int)$group['leader_student_id']; ?>;
      var selectedId = parseInt(studentSel.value || '0', 10);
      if (selectedId === currentLeaderId) {
        ev.preventDefault();
        var warn = document.createElement('div');
        warn.className = 'modal fade';
        warn.id = 'leaderRequiredModal';
        warn.innerHTML = '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Leader obbligatorio</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p>Ogni gruppo deve avere un leader. Non puoi impostare l’unico leader come installer.</p></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button></div></div></div>';
        document.body.appendChild(warn);
        var wm = new bootstrap.Modal(warn);
        wm.show();
        warn.addEventListener('hidden.bs.modal', function(){ warn.remove(); }, { once: true });
      }
    }
  });
</script>
<?php } ?>
<div class="modal fade" id="deleteLaptopModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteLaptopForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma disassociazione PC</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sei sicuro di disassociare il PC <span id="delLaptopCode"></span> da questo gruppo?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-danger">Elimina</button>
        </div>
      </form>
    </div>
  </div>
</div>
