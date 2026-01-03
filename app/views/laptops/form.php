<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo $laptop ? 'Modifica Laptop' : 'Nuovo Laptop'; ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url((isset($laptop['id']) && $laptop['id']) ? '/laptops/'.$laptop['id'].'/update' : '/laptops'); ?>">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="code">Codice</label>
          <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($laptop['code']??''); ?>" required autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="brand_model">Modello</label>
          <input type="text" class="form-control" id="brand_model" name="brand_model" value="<?php echo htmlspecialchars($laptop['brand_model']??''); ?>" required autocomplete="off">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="cpu">CPU</label>
          <input type="text" class="form-control" id="cpu" name="cpu" value="<?php echo htmlspecialchars($laptop['cpu']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="ram">RAM</label>
          <input type="text" class="form-control" id="ram" name="ram" value="<?php echo htmlspecialchars($laptop['ram']??''); ?>" autocomplete="off">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="storage">Storage</label>
          <input type="text" class="form-control" id="storage" name="storage" value="<?php echo htmlspecialchars($laptop['storage']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="screen">Schermo</label>
          <input type="text" class="form-control" id="screen" name="screen" value="<?php echo htmlspecialchars($laptop['screen']??''); ?>" autocomplete="off">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="tech_notes">Note tecniche</label>
        <textarea class="form-control" id="tech_notes" name="tech_notes" rows="3" autocomplete="off"><?php echo htmlspecialchars($laptop['tech_notes']??''); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label" for="scratches">Graffi</label>
        <textarea class="form-control" id="scratches" name="scratches" rows="2" autocomplete="off"><?php echo htmlspecialchars($laptop['scratches']??''); ?></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Condizioni fisiche</label>
          <?php $__labels = ['excellent'=>'Eccellente','very_good'=>'Molto buone','good'=>'Buone','average'=>'Discrete','fair'=>'Sufficienti','poor'=>'Scarse']; ?>
          <select class="form-select" name="physical_condition">
            <?php foreach ($__labels as $val=>$label) { ?>
              <option value="<?php echo $val; ?>" <?php echo (($laptop['physical_condition']??'')===$val)?'selected':''; ?>><?php echo $label; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Batteria</label>
          <?php $__labels_batt = ['excellent'=>'Eccellente','very_good'=>'Molto buone','good'=>'Buone','average'=>'Discrete','fair'=>'Sufficienti','poor'=>'Scarse']; ?>
          <select class="form-select" name="battery">
            <?php foreach ($__labels_batt as $val=>$label) { ?>
              <option value="<?php echo $val; ?>" <?php echo (($laptop['battery']??'')===$val)?'selected':''; ?>><?php echo $label; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Livello condizione generale</label>
        <?php $__labels_lvl = ['excellent'=>'Eccellente','very_good'=>'Molto buone','good'=>'Buone','average'=>'Discrete','fair'=>'Sufficienti','poor'=>'Scarse']; ?>
        <select class="form-select" name="condition_level">
          <?php foreach ($__labels_lvl as $val=>$label) { ?>
            <option value="<?php echo $val; ?>" <?php echo (($laptop['condition_level']??'good')===$val)?'selected':''; ?>><?php echo $label; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Software da installare</label>
        <div>
          <?php foreach (($softwares??[]) as $s) { if ($s['name']==='Windows') { $checked = in_array($s['id'], ($selected_software_ids??[])); ?>
            <div class="d-flex align-items-center mb-2">
              <input class="form-check-input me-2" type="checkbox" data-name="Windows" name="software_ids[]" id="soft_<?php echo $s['id']; ?>" value="<?php echo $s['id']; ?>" <?php echo $checked?'checked':''; ?>>
              <label class="form-check-label text-nowrap me-3" for="soft_<?php echo $s['id']; ?>" style="min-width:160px;height:38px;line-height:38px"><?php echo htmlspecialchars('Windows'.($s['version']?' '.$s['version']:'')); ?></label>
              <label for="windows_license" class="visually-hidden">Licenza Windows</label>
              <input type="text" class="form-control" id="windows_license" name="windows_license" placeholder="Licenza Windows" value="<?php echo htmlspecialchars($laptop['windows_license']??''); ?>" style="width:260px; flex:0 0 260px; height:38px" autocomplete="off">
            </div>
          <?php }} ?>
          <?php foreach (($softwares??[]) as $s) { if ($s['name']==='Office') { $checked = in_array($s['id'], ($selected_software_ids??[])); ?>
            <div class="d-flex align-items-center mb-2">
              <input class="form-check-input me-2" type="checkbox" data-name="Office" name="software_ids[]" id="soft_<?php echo $s['id']; ?>" value="<?php echo $s['id']; ?>" <?php echo $checked?'checked':''; ?>>
              <label class="form-check-label text-nowrap me-3" for="soft_<?php echo $s['id']; ?>" style="min-width:160px;height:38px;line-height:38px"><?php echo htmlspecialchars('Office'.($s['version']?' '.$s['version']:'')); ?></label>
              <label for="office_license" class="visually-hidden">Licenza Office</label>
              <input type="text" class="form-control" id="office_license" name="office_license" placeholder="Licenza Office" value="<?php echo htmlspecialchars($laptop['office_license']??''); ?>" style="width:260px; flex:0 0 260px; height:38px" autocomplete="off">
            </div>
          <?php }} ?>
          <?php foreach (($softwares??[]) as $s) { if ($s['name']!=='Windows' && $s['name']!=='Office') { $checked = in_array($s['id'], ($selected_software_ids??[])); ?>
            <div class="d-flex align-items-center mb-2">
              <input class="form-check-input me-2" type="checkbox" data-name="<?php echo htmlspecialchars($s['name']); ?>" name="software_ids[]" id="soft_<?php echo $s['id']; ?>" value="<?php echo $s['id']; ?>" <?php echo $checked?'checked':''; ?>>
              <label class="form-check-label text-nowrap me-3" for="soft_<?php echo $s['id']; ?>" style="min-width:160px;height:38px;line-height:38px">
                <?php echo htmlspecialchars($s['name'].($s['version']?' '.$s['version']:'')); ?>
              </label>
              <div class="me-3" style="width:260px; flex:0 0 260px; height:38px"></div>
              <?php if ($s['cost']!==null) { ?>
                <span class="badge bg-primary-subtle text-primary"><?php echo number_format((float)$s['cost'],2,',','.'); ?> €</span>
              <?php } ?>
            </div>
          <?php }} ?>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="other_software_request">Note software</label>
        <textarea class="form-control" id="other_software_request" name="other_software_request" rows="2" autocomplete="off"><?php echo htmlspecialchars($laptop['other_software_request']??''); ?></textarea>
      </div>
      <script>
      (function(){
        var officeInput=document.getElementById('office_license');
        var windowsInput=document.getElementById('windows_license');
        var boxes=document.querySelectorAll('input[type="checkbox"][name="software_ids[]"]');
        function update(){
          var officeChecked=false,windowsChecked=false;
          boxes.forEach(function(b){
            var n=b.getAttribute('data-name');
            if(n==='Office'){ if(b.checked) officeChecked=true; }
            if(n==='Windows'){ if(b.checked) windowsChecked=true; }
          });
          officeInput.disabled=!officeChecked;
          windowsInput.disabled=!windowsChecked;
        }
        boxes.forEach(function(b){ b.addEventListener('change',update); });
        update();
      })();
      </script>
      <div class="mb-3">
        <label class="form-label">Stato</label>
        <?php $__status_labels = ['in_progress'=>'In lavorazione','ready'=>'Pronto','missing_software'=>'Manca software','to_check'=>'Da verificare','delivered'=>'Consegnato']; ?>
        <select class="form-select" name="status">
          <?php foreach ($__status_labels as $val=>$label) { ?>
            <option value="<?php echo $val; ?>" <?php echo (($laptop['status']??'in_progress')===$val)?'selected':''; ?>><?php echo $label; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Docente</label>
          <select class="form-select" name="customer_id">
            <option value="">Nessuno</option>
            <?php foreach ($customers as $c) { ?>
              <option value="<?php echo $c['id']; ?>" <?php echo (($laptop['customer_id']??'')==$c['id'])?'selected':''; ?>><?php echo htmlspecialchars($c['last_name'].' '.$c['first_name']); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Gruppo</label>
          <select class="form-select" name="group_id">
            <option value="">Nessuno</option>
            <?php foreach ($groups as $g) { ?>
              <option value="<?php echo $g['id']; ?>" <?php echo (($laptop['group_id']??'')==$g['id'])?'selected':''; ?>><?php echo htmlspecialchars($g['name']); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="<?php echo \App\Core\Helpers::url('/laptops'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>

