<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo $item ? 'Modifica Software' : 'Nuovo Software'; ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url($item ? '/software/'.$item['id'].'/update' : '/software'); ?>">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="soft_name">Nome</label>
          <input type="text" class="form-control" id="soft_name" name="name" value="<?php echo htmlspecialchars($item['name']??''); ?>" required autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="soft_version">Versione</label>
          <input type="text" class="form-control" id="soft_version" name="version" value="<?php echo htmlspecialchars($item['version']??''); ?>" autocomplete="off">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="soft_license">Licenza</label>
          <?php $licenses = ['Commercial'=>'Commerciale','Free'=>'Gratuita','GPL'=>'GPL','MIT'=>'MIT','MPL'=>'MPL']; ?>
          <select class="form-select" id="soft_license" name="license">
            <option value="" <?php echo ($item['license']??'')===''?'selected':''; ?>>Nessuna</option>
            <?php foreach ($licenses as $val=>$label) { ?>
              <option value="<?php echo $val; ?>" <?php echo (($item['license']??'')===$val)?'selected':''; ?>><?php echo $label; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="soft_cost">Costo (€)</label>
          <input type="number" step="0.01" class="form-control" id="soft_cost" name="cost" value="<?php echo htmlspecialchars($item['cost']??''); ?>" autocomplete="off">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="soft_notes">Note</label>
        <textarea class="form-control" id="soft_notes" name="notes" rows="2" autocomplete="off"><?php echo htmlspecialchars($item['notes']??''); ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="<?php echo \App\Core\Helpers::url('/software'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
