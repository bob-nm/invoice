<?php
// companies_api.php
require 'config.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? $_POST['action'] ?? ($_SERVER['REQUEST_METHOD']==='POST' ? 'add' : null);

if(($action === 'add') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $prefix = strtoupper(substr(trim($data['prefix'] ?? ''),0,2));
    if(!$name || !$prefix) { echo json_encode(['success'=>false,'message'=>'name/prefix required']); exit; }
    $stmt = $pdo->prepare("INSERT INTO companies (name, code_prefix) VALUES (?, ?)");
    $stmt->execute([$name, $prefix]);
    $id = $pdo->lastInsertId();
    $company = $pdo->query("SELECT * FROM companies WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true,'company'=>$company]); exit;
}

if($action === 'next_serial') {
    $prefix = $_GET['prefix'] ?? '';
    $prefix = preg_replace('/[^A-Z]/','', strtoupper($prefix));
    if(!$prefix) { echo json_encode(['success'=>false,'message'=>'prefix required']); exit; }
    // serial logic: count invoices today for this prefix then +1
    $today = date('Y-m-d');
    $yy = date('y'); $mm = date('m'); $dd = date('d');
    $like = $prefix . $yy . $mm . $dd . '%';
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM invoices WHERE invoice_number LIKE ?");
    $stmt->execute([$like]);
    $cnt = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;
    $serial = $cnt + 1;
    echo json_encode(['success'=>true,'serial'=>$serial]); exit;
}

echo json_encode(['success'=>false,'message'=>'invalid action']);
