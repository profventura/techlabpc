<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Laptop;
use App\Models\Customer;
use App\Models\WorkGroup;
class LaptopController {
  public function index() {
    Auth::require();
    $model = new Laptop();
    $filters = [
      'status' => $_GET['status'] ?? null,
      'customer_id' => $_GET['customer_id'] ?? null,
      'group_id' => $_GET['group_id'] ?? null,
    ];
    $laptops = $model->all($filters);
    $customers = (new Customer())->all();
    $groups = (new WorkGroup())->all();
    Helpers::view('laptops/index', ['title'=>'Laptops','laptops'=>$laptops,'customers'=>$customers,'groups'=>$groups,'filters'=>$filters]);
  }
  public function show($id) {
    Auth::require();
    $model = new Laptop();
    $l = $model->find($id);
    $history = $model->stateHistory($id);
    $software_list = $model->softwareList($id);
    Helpers::view('laptops/show', ['title'=>'Laptop','laptop'=>$l,'history'=>$history,'software_list'=>$software_list]);
  }
  public function createForm() {
    Auth::require();
    $customers = (new Customer())->all();
    $groups = (new WorkGroup())->all();
    $softwares = (new \App\Models\Software())->all();
    $config = require __DIR__ . '/../config.php';
    $d = $config['defaults']['laptop'] ?? [];
    $laptopDefaults = [
      'status' => $d['status'] ?? 'in_progress',
      'condition_level' => $d['condition_level'] ?? 'good',
      'physical_condition' => $d['physical_condition'] ?? 'good',
      'battery' => $d['battery'] ?? 'good',
      'brand_model' => $d['brand_model'] ?? '',
      'cpu' => $d['cpu'] ?? '',
      'ram' => $d['ram'] ?? '',
      'storage' => $d['storage'] ?? '',
      'screen' => $d['screen'] ?? '',
      'office_license' => '',
      'windows_license' => '',
      'other_software_request' => '',
    ];
    Helpers::view('laptops/form', ['title'=>'Nuovo Laptop','customers'=>$customers,'groups'=>$groups,'laptop'=>$laptopDefaults,'softwares'=>$softwares,'selected_software_ids'=>[]]);
  }
  public function store() {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $config = require __DIR__ . '/../config.php';
    $d = $config['defaults']['laptop'] ?? [];
    $data = [
      'code'=>trim($_POST['code'] ?? ''),
      'brand_model'=>($bm=trim($_POST['brand_model'] ?? ''))!=='' ? $bm : ($d['brand_model'] ?? ''),
      'cpu'=>($cpu=trim($_POST['cpu'] ?? ''))!=='' ? $cpu : ($d['cpu'] ?? ''),
      'ram'=>($ram=trim($_POST['ram'] ?? ''))!=='' ? $ram : ($d['ram'] ?? ''),
      'storage'=>($st=trim($_POST['storage'] ?? ''))!=='' ? $st : ($d['storage'] ?? ''),
      'screen'=>($sc=trim($_POST['screen'] ?? ''))!=='' ? $sc : ($d['screen'] ?? ''),
      'tech_notes'=>trim($_POST['tech_notes'] ?? ''),
      'scratches'=>trim($_POST['scratches'] ?? ''),
      'physical_condition'=>trim($_POST['physical_condition'] ?? ($d['physical_condition'] ?? 'good')),
      'battery'=>trim($_POST['battery'] ?? ($d['battery'] ?? 'good')),
      'condition_level'=>$_POST['condition_level'] ?? ($d['condition_level'] ?? 'good'),
      'office_license'=>($t=trim($_POST['office_license'] ?? ''))!=='' ? $t : null,
      'windows_license'=>($w=trim($_POST['windows_license'] ?? ''))!=='' ? $w : null,
      'other_software_request'=>($o=trim($_POST['other_software_request'] ?? ''))!=='' ? $o : null,
      'status'=>$_POST['status'] ?? ($d['status'] ?? 'in_progress'),
      'customer_id'=>$_POST['customer_id'] ?? null,
      'group_id'=>$_POST['group_id'] ?? null,
    ];
    $model = new Laptop();
    $id = $model->create($data);
    $softwareIds = array_map('intval', $_POST['software_ids'] ?? []);
    $model->setSoftwares($id, $softwareIds);
    $actor = Auth::user()['id'] ?? null;
    $model->addStateHistory($id, $actor, null, $data['status'], null);
    $model->logStatusChange($id, $actor, $data['customer_id'] ?? null, $data['group_id'] ?? null, null);
    Helpers::redirect('/laptops/'.$id);
  }
  public function editForm($id) {
    Auth::require();
    $model = new Laptop();
    $laptop = $model->find($id);
    $customers = (new Customer())->all();
    $groups = (new WorkGroup())->all();
    $softwares = (new \App\Models\Software())->all();
    $selected = $model->softwareIds($id);
    Helpers::view('laptops/form', ['title'=>'Modifica Laptop','customers'=>$customers,'groups'=>$groups,'laptop'=>$laptop,'softwares'=>$softwares,'selected_software_ids'=>$selected]);
  }
  public function update($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = [
      'code'=>trim($_POST['code'] ?? ''),
      'brand_model'=>trim($_POST['brand_model'] ?? ''),
      'cpu'=>trim($_POST['cpu'] ?? ''),
      'ram'=>trim($_POST['ram'] ?? ''),
      'storage'=>trim($_POST['storage'] ?? ''),
      'screen'=>trim($_POST['screen'] ?? ''),
      'tech_notes'=>trim($_POST['tech_notes'] ?? ''),
      'scratches'=>trim($_POST['scratches'] ?? ''),
      'physical_condition'=>trim($_POST['physical_condition'] ?? ''),
      'battery'=>trim($_POST['battery'] ?? ''),
      'condition_level'=>$_POST['condition_level'] ?? 'good',
      'office_license'=>($t=trim($_POST['office_license'] ?? ''))!=='' ? $t : null,
      'windows_license'=>($w=trim($_POST['windows_license'] ?? ''))!=='' ? $w : null,
      'other_software_request'=>($o=trim($_POST['other_software_request'] ?? ''))!=='' ? $o : null,
      'status'=>$_POST['status'] ?? 'in_progress',
      'customer_id'=>$_POST['customer_id'] ?? null,
      'group_id'=>$_POST['group_id'] ?? null,
    ];
    $model = new Laptop();
    $existing = $model->find($id);
    $prevStatus = $existing['status'] ?? null;
    $model->update($id, $data);
    $softwareIds = array_map('intval', $_POST['software_ids'] ?? []);
    $model->setSoftwares($id, $softwareIds);
    if ($prevStatus !== $data['status']) {
      $actor = Auth::user()['id'] ?? null;
      $model->addStateHistory($id, $actor, $prevStatus, $data['status'], null);
      $model->logStatusChange($id, $actor, $existing['customer_id'] ?? null, $existing['group_id'] ?? null, null);
    }
    Helpers::redirect('/laptops/'.$id);
  }
  public function delete($id) {
    Auth::require();
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Laptop())->delete($id);
    Helpers::redirect('/laptops');
  }
}

