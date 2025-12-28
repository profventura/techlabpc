<h2 class="mb-4">Dashboard</h2>
<div class="row">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">PC totali</h5>
        <h2 class="card-text text-primary"><?php echo $counts['laptops_total']; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">Pronti</h5>
        <h2 class="card-text text-success"><?php echo $counts['ready']; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">In lavorazione</h5>
        <h2 class="card-text text-warning"><?php echo $counts['in_progress']; ?></h2>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">Manca software</h5>
        <h2 class="card-text text-danger"><?php echo $counts['missing_software']; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">Da verificare</h5>
        <h2 class="card-text text-warning"><?php echo $counts['to_check']; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">Consegnati</h5>
        <h2 class="card-text text-success"><?php echo $counts['delivered']; ?></h2>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">Docenti totali</h5>
        <h2 class="card-text text-primary"><?php echo $counts['customers_total']; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">Studenti totali</h5>
        <h2 class="card-text text-primary"><?php echo $counts['students_total']; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold">Gruppi totali</h5>
        <h2 class="card-text text-primary"><?php echo $counts['groups_total']; ?></h2>
      </div>
    </div>
  </div>
</div>
