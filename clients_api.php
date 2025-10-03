<?php
require 'config.php';
header('Content-Type: application/json');
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if(!$data || empty($data['name'])) {
  echo json_encode(['success'=>false,'message'=>'name needed']); exit;
}
$name = trim($data['name']);
$stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (?)");
$stmt->execute([$name]);
$id = $pdo->lastInsertId();
$client = $pdo->query("SELECT * FROM clients WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
echo json_encode(['success'=>true,'client'=>$client]);
