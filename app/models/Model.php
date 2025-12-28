<?php
namespace App\Models;
use App\Core\DB;
class Model {
  protected $pdo;
  public function __construct() { $this->pdo = DB::conn(); }
}

