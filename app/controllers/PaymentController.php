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
    Helpers::view('payments/form', ['title'=>'Nuovo bonifico','customers'=>$customers,'payment'=>null]);
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
      'pcs_paid_count'=>intval($_POST['pcs_paid_count'] ?? 0),
      'status'=>$_POST['status'] ?? 'pending'
    ];
    (new PaymentTransfer())->create($data);
    (new \App\Models\Log())->addAction('create_payment', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$_POST['customer_id'] ?? null, 'note'=>($_POST['amount'].' '.$_POST['paid_at'].' pcs '.$data['pcs_paid_count'])]);
    Helpers::redirect('/payments');
  }
  public function editForm($id) {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    $payment = (new PaymentTransfer())->find($id);
    $customers = (new Customer())->all();
    Helpers::view('payments/form', ['title'=>'Modifica bonifico','customers'=>$customers,'payment'=>$payment]);
  }
  public function update($id) {
    Auth::require();
    if (!Auth::isAdmin()) { Helpers::redirect('/'); return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $model = new PaymentTransfer();
    $existing = $model->find($id);
    $config = require __DIR__ . '/../config.php';
    $newReceipt = Helpers::fileUpload('receipt', $config['app']['upload_dir']);
    $receiptPath = $existing['receipt_path'];
    if ($newReceipt) {
      if (!empty($receiptPath) && strpos($receiptPath, 'public/uploads/') === 0) {
        $abs = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $receiptPath);
        if (is_file($abs)) { @unlink($abs); }
      }
      $receiptPath = $newReceipt;
    }
    $data = [
      'customer_id'=>$_POST['customer_id'],
      'amount'=>$_POST['amount'],
      'paid_at'=>$_POST['paid_at'],
      'reference'=>trim($_POST['reference'] ?? ''),
      'receipt_path'=>$receiptPath,
      'pcs_paid_count'=>intval($_POST['pcs_paid_count'] ?? 0),
      'status'=>$_POST['status'] ?? 'pending'
    ];
    $model->update($id, $data);
    (new \App\Models\Log())->addAction('update_payment', \App\Core\Auth::user()['id'] ?? null, ['customer_id'=>$_POST['customer_id'] ?? null, 'note'=>($_POST['amount'].' '.$_POST['paid_at'].' pcs '.$data['pcs_paid_count'])]);
    Helpers::redirect('/payments');
  }
  public function delete($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $model = new PaymentTransfer();
    $p = $model->find($id);
    if (!empty($p['receipt_path']) && strpos($p['receipt_path'], 'public/uploads/') === 0) {
      $abs = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $p['receipt_path']);
      if (is_file($abs)) { @unlink($abs); }
    }
    $model->delete($id);
    (new \App\Models\Log())->addAction('delete_payment', \App\Core\Auth::user()['id'] ?? null, ['note'=>strval($id)]);
    Helpers::redirect('/payments');
  }
}
