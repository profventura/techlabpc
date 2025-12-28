<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\PaymentTransfer;
use App\Models\Customer;
class PaymentController {
  public function index() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    $payments = (new PaymentTransfer())->all();
    Helpers::view('payments/index', ['title'=>'Pagamenti','payments'=>$payments]);
  }
  public function createForm() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    $customers = (new Customer())->all();
    Helpers::view('payments/form', ['title'=>'Nuovo Bonifico','customers'=>$customers]);
  }
  public function store() {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $config = require __DIR__ . '/../config.php';
    $receipt = Helpers::fileUpload('receipt', $config['app']['upload_dir']);
    $data = [
      'customer_id'=>$_POST['customer_id'],
      'amount'=>$_POST['amount'],
      'paid_at'=>$_POST['paid_at'],
      'reference'=>trim($_POST['reference'] ?? ''),
      'receipt_path'=>$receipt,
      'status'=>$_POST['status'] ?? 'pending'
    ];
    (new PaymentTransfer())->create($data);
    Helpers::redirect('/payments');
  }
}

