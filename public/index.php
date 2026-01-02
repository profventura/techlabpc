<?php
declare(strict_types=1);
session_start();
spl_autoload_register(function($class){
  $prefix = 'App\\';
  if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
  $path = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
  if (file_exists($path)) require $path;
});
use App\Core\Router;
use App\Core\Auth;
use App\Core\Helpers;
use App\Core\DB;
use App\Controllers\AuthController;
use App\Controllers\LaptopController;
use App\Controllers\CustomerController;
use App\Controllers\StudentController;
use App\Controllers\WorkGroupController;
use App\Controllers\PaymentController;
use App\Controllers\SoftwareController;
use App\Controllers\LogsController;
$router = new Router();
$pdoBootstrap = DB::conn();
// Ensure superuser admin exists
$adminExists = (int)$pdoBootstrap->query("SELECT COUNT(*) c FROM students WHERE email='admin'")->fetch()['c'];
if ($adminExists === 0) {
  $stmt = $pdoBootstrap->prepare('INSERT INTO students (first_name,last_name,email,password_hash,role,active) VALUES (?,?,?,?,?,?)');
  $stmt->execute(['Super','Admin','admin',password_hash('admin', PASSWORD_DEFAULT),'admin',1]);
}
// Default seed if empty (optional, but keeping logical flow)
$exists = (int)$pdoBootstrap->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
if ($exists === 0) {
  $stmt = $pdoBootstrap->prepare('INSERT INTO students (first_name,last_name,email,password_hash,role,active) VALUES (?,?,?,?,?,?)');
  $stmt->execute(['Admin','User','admin@example.com',password_hash('admin123', PASSWORD_DEFAULT),'admin',1]);
}
$router->get('/login', [AuthController::class,'loginForm']);
$router->post('/login', [AuthController::class,'login']);
$router->get('/logout', [AuthController::class,'logout']);
$router->get('/', function(){
  if (!Auth::check()) { Helpers::redirect('/login'); }
  $pdo = DB::conn();
  $counts = [];
  $counts['laptops_total'] = (int)$pdo->query('SELECT COUNT(*) c FROM laptops')->fetch()['c'];
  foreach (['ready','in_progress','missing_software','to_check','delivered'] as $st) {
    $stc = $pdo->prepare('SELECT COUNT(*) c FROM laptops WHERE status=?');
    $stc->execute([$st]);
    $counts[$st] = (int)$stc->fetch()['c'];
  }
  $counts['customers_total'] = (int)$pdo->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];
  $counts['students_total'] = (int)$pdo->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
  $counts['groups_total'] = (int)$pdo->query('SELECT COUNT(*) c FROM work_groups')->fetch()['c'];
  Helpers::view('dashboard', ['title'=>'Dashboard','counts'=>$counts]);
});
$router->get('/laptops', [LaptopController::class,'index']);
$router->get('/laptops/export', [LaptopController::class,'export']);
$router->post('/laptops/import', [LaptopController::class,'import']);
$router->get('/laptops/create', [LaptopController::class,'createForm']);
$router->post('/laptops', [LaptopController::class,'store']);
$router->get('/laptops/{id}', [LaptopController::class,'show']);
$router->get('/laptops/{id}/edit', [LaptopController::class,'editForm']);
$router->post('/laptops/{id}/update', [LaptopController::class,'update']);
$router->post('/laptops/{id}/delete', [LaptopController::class,'delete']);
$router->get('/customers', [CustomerController::class,'index']);
$router->get('/customers/export', [CustomerController::class,'export']);
$router->post('/customers/import', [CustomerController::class,'import']);
$router->get('/customers/create', [CustomerController::class,'createForm']);
$router->post('/customers', [CustomerController::class,'store']);
$router->get('/customers/{id}', [CustomerController::class,'show']);
$router->get('/customers/{id}/edit', [CustomerController::class,'editForm']);
$router->post('/customers/{id}/update', [CustomerController::class,'update']);
$router->post('/customers/{id}/delete', [CustomerController::class,'delete']);
$router->get('/students', [StudentController::class,'index']);
$router->get('/students/export', [StudentController::class,'export']);
$router->post('/students/import', [StudentController::class,'import']);
$router->get('/students/create', [StudentController::class,'createForm']);
$router->post('/students', [StudentController::class,'store']);
$router->get('/students/{id}/edit', [StudentController::class,'editForm']);
$router->post('/students/{id}/update', [StudentController::class,'update']);
$router->post('/students/{id}/delete', [StudentController::class,'delete']);
$router->get('/work-groups', [WorkGroupController::class,'index']);
$router->get('/work-groups/create', [WorkGroupController::class,'createForm']);
$router->post('/work-groups', [WorkGroupController::class,'store']);
$router->get('/work-groups/{id}', [WorkGroupController::class,'show']);
$router->get('/work-groups/{id}/edit', [WorkGroupController::class,'editForm']);
$router->post('/work-groups/{id}/update', [WorkGroupController::class,'update']);
$router->post('/work-groups/{id}/add-member', [WorkGroupController::class,'addMember']);
$router->post('/work-groups/{id}/remove-member', [WorkGroupController::class,'removeMember']);
$router->get('/work-groups/export', [WorkGroupController::class,'export']);
$router->post('/work-groups/import', [WorkGroupController::class,'import']);
$router->post('/work-groups/{id}/delete', [WorkGroupController::class,'delete']);
$router->get('/payments', [PaymentController::class,'index']);
$router->get('/payments/create', [PaymentController::class,'createForm']);
$router->post('/payments', [PaymentController::class,'store']);
$router->get('/payments/{id}/edit', [PaymentController::class,'editForm']);
$router->post('/payments/{id}/update', [PaymentController::class,'update']);
$router->post('/payments/{id}/delete', [PaymentController::class,'delete']);
$router->get('/logs', [LogsController::class,'index']);
$router->get('/software', [SoftwareController::class,'index']);
$router->get('/software/create', [SoftwareController::class,'createForm']);
$router->post('/software', [SoftwareController::class,'store']);
$router->get('/software/{id}/edit', [SoftwareController::class,'editForm']);
$router->post('/software/{id}/update', [SoftwareController::class,'update']);
$router->post('/software/{id}/delete', [SoftwareController::class,'delete']);
$router->dispatch();

