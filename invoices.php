<?php
// invoices.php
require 'config.php';
$q = $_GET['q'] ?? '';
$sql = "SELECT i.*, c.name AS company_name, cl.name AS client_name FROM invoices i
        JOIN companies c ON i.company_id=c.id
        JOIN clients cl ON i.client_id=cl.id
        WHERE i.invoice_number LIKE :q OR cl.name LIKE :q OR c.name LIKE :q
        ORDER BY i.created_at DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute([':q'=> "%$q%"]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Invoices</title></head><body>
<h1>Invoices</h1>
<form><input name="q" value="<?=htmlspecialchars($q)?>"><button>Search</button></form>
<table border="1" cellpadding="6"><tr><th>#</th><th>Invoice</th><th>Company</th><th>Client</th><th>Total</th><th>Actions</th></tr>
<?php foreach($rows as $r): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= htmlspecialchars($r['invoice_number']) ?><br><?=htmlspecialchars($r['issue_date'])?></td>
  <td><?=htmlspecialchars($r['company_name'])?></td>
  <td><?=htmlspecialchars($r['client_name'])?></td>
  <td><?=number_format($r['total'],2)?></td>
  <td>
    <a href="view_invoice.php?id=<?=$r['id']?>">View</a> |
    <a href="generate_pdf.php?id=<?=$r['id']?>">PDF</a> |
    <a href="edit_invoice.php?id=<?=$r['id']?>">Edit</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
</body></html>
