<?php
// Load dompdf
require_once __DIR__ . '/vendor/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// --- Example: fetch invoice data from database (simplified) ---
$invoiceId = $_GET['id']; // e.g. generate_pdf.php?id=5

// Here you’d normally query your DB for company, client, items, totals
// For testing, let’s just hardcode:
$companyName = "OrthoWalk Ltd.";
$clientName = "Test Client";
$items = [
    ["desc" => "Website Design", "qty" => 1, "price" => 1000],
    ["desc" => "Hosting (12 months)", "qty" => 1, "price" => 200],
];

// Build HTML for invoice
$html = "<h1>Invoice #$invoiceId</h1>";
$html .= "<p><strong>From:</strong> $companyName</p>";
$html .= "<p><strong>To:</strong> $clientName</p>";
$html .= "<table border='1' cellspacing='0' cellpadding='6'>
            <tr><th>Description</th><th>Qty</th><th>Price</th><th>Total</th></tr>";

$total = 0;
foreach ($items as $item) {
    $lineTotal = $item['qty'] * $item['price'];
    $total += $lineTotal;
    $html .= "<tr>
                <td>{$item['desc']}</td>
                <td>{$item['qty']}</td>
                <td>{$item['price']}</td>
                <td>$lineTotal</td>
              </tr>";
}
$html .= "<tr><td colspan='3'><strong>Total</strong></td><td><strong>$total</strong></td></tr>";
$html .= "</table>";

// --- Use dompdf ---
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("invoice_$invoiceId.pdf", ["Attachment" => false]); // false = open in browser
?>
