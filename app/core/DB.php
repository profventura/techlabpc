<?php
namespace App\Core;
use PDO;
class DB {
  private static $pdo;
  public static function conn() {
    if (!self::$pdo) {
      $config = require __DIR__ . '/../config.php';
      $host = $config['db']['host'];
      $port = $config['db']['port'];
      $name = $config['db']['name'];
      $charset = $config['db']['charset'];
      $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
      $user = $config['db']['user'];
      $pass = $config['db']['pass'];
      self::$pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    }
    return self::$pdo;
  }
}
