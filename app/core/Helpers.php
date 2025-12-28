<?php
namespace App\Core;
class Helpers {
  public static function url($path = '') {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base = $scriptDir === '/' ? '' : $scriptDir;
    return $base . '/' . ltrim($path, '/');
  }
  public static function redirect($path) {
    if (!preg_match('#^https?://#', $path)) {
      $path = self::url($path);
    }
    header('Location: '.$path); exit;
  }
  public static function view($template, $data = []) { extract($data); require __DIR__ . '/../views/layout.php'; }
  public static function param($key, $default = null) { return $_POST[$key] ?? $_GET[$key] ?? $default; }
  public static function fileUpload($fileKey, $destDir) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) return null;
    $name = basename($_FILES[$fileKey]['name']);
    $safe = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','_', $name);
    if (!is_dir($destDir)) { mkdir($destDir, 0777, true); }
    $path = rtrim($destDir,'/\\') . DIRECTORY_SEPARATOR . $safe;
    move_uploaded_file($_FILES[$fileKey]['tmp_name'], $path);
    return 'public/uploads/'.$safe;
  }
}
