<?php
// save_invoice.php
require 'config.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  die('Invalid request');
}

$data = $_POST;
$company_id = (int)$data['company_id'];
$client_id = (int)$data['client_id'];
$invoice_number = trim($data['invoice_number']) ?: null;
$issue_date = $data['issue_date'] ?? date('Y-m-d');
$due_date = $data['due_date'] ?? date('Y-m-d', strtotime('+14 days'));
$notes = $data['notes'] ?? '';

$items = $data['items'] ?? [];
if(!$company_id || !$client_id || empty($items)) die('Missing fields');

$subtotal = 0; $tax_total = 0;
foreach($items as $it) {
  $qty = (float)($it['qty'] ?? 0);
  $unit = (float)($it['unit_price'] ?? 0);
  $taxr = (float)($it['tax_rate'] ?? 0);
  $line = $qty * $unit;
  $linetax = $line * ($taxr/100);
  $subtotal += $line;
  $tax_total += $linetax;
}
$total = $subtotal + $tax_total;

// if invoice_number is empty, generate server-side to avoid dup
if(!$invoice_number) {
  // find company prefix
  $stmt = $pdo->prepare("SELECT code_prefix FROM companies WHERE id=?");
  $stmt->execute([$company_id]);
  $prefix = $stmt->fetchColumn() ?: 'XX';
  $yy = date('y'); $mm = date('m'); $dd = date('d');
  // get serial count
  $like = $prefix . $yy . $mm . $dd . '%';
  $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM invoices WHERE invoice_number LIKE ?");
  $stmt->execute([$like]);
  $cnt = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;
  $serial = $cnt + 1;
  $invoice_number = $prefix . $yy . $mm . $dd . str_pad($serial,3,'0',STR_PAD_LEFT);
}

// ensure unique (very rare race condition)
$try = 0;
while($try < 5) {
  try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO invoices (company_id, client_id, invoice_number, issue_date, due_date, subtotal, tax_total, total, notes) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$company_id,$client_id,$invoice_number,$issue_date,$due_date,$subtotal,$tax_total,$total,$notes]);
    $inv_id = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare("INSERT INTO invoice_items (invoice_id, description, qty, unit_price, tax_rate, line_total) VALUES (?,?,?,?,?,?)");
    foreach($items as $it) {
      $qty = (float)($it['qty'] ?? 0);
      $unit = (float)($it['unit_price'] ?? 0);
      $taxr = (float)($it['tax_rate'] ?? 0);
      $line = $qty * $unit;
      $linetax = $line * ($taxr/100);
      $line_total = $line + $linetax;
      $stmtItem->execute([$inv_id, $it['description'], $qty, $unit, $taxr, $line_total]);
    }

    $pdo->commit();
    header("Location: generate_pdf.php?id=" . $inv_id);
    exit;
  } catch (PDOException $e) {
    $pdo->rollBack();
    if(strpos($e->getMessage(), '1062') !== false) {
      // duplicate invoice_number — regenerate
      $invoice_number = $prefix . date('y') . date('m') . date('d') . str_pad(rand(1,999),3,'0',STR_PAD_LEFT);
      $try++;
    } else {
      die('DB error: ' . $e->getMessage());
    }
  }
}

die('Could not save invoice');
