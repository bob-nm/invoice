<?php
// index.php
require 'config.php';

// load companies & clients
$companies = $pdo->query("SELECT * FROM companies ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$clients = $pdo->query("SELECT * FROM clients ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Invoice</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <h1>Create Invoice</h1>

  <form id="invoiceForm" action="save_invoice.php" method="post">
    <div class="row">
      <label>Company:
        <select id="company_id" name="company_id" required>
          <option value="">-- choose company --</option>
          <?php foreach($companies as $c): ?>
            <option value="<?= $c['id'] ?>" data-prefix="<?=htmlspecialchars($c['code_prefix'])?>"><?=htmlspecialchars($c['name'])?></option>
          <?php endforeach; ?>
        </select>
        <button type="button" id="addCompanyBtn">+ add</button>
      </label>

      <label>Client:
        <select id="client_id" name="client_id" required>
          <option value="">-- choose client --</option>
          <?php foreach($clients as $cl): ?>
            <option value="<?= $cl['id'] ?>"><?=htmlspecialchars($cl['name'])?></option>
          <?php endforeach; ?>
        </select>
        <button type="button" id="addClientBtn">+ add</button>
      </label>
    </div>

    <div class="row">
      <label>Invoice Number:
        <input type="text" id="invoice_number" name="invoice_number" placeholder="auto or type" />
        <button type="button" id="genNumberBtn">Auto-generate</button>
      </label>
      <label>Issue Date:
        <input type="date" id="issue_date" name="issue_date" />
      </label>
      <label>Due Date:
        <input type="date" id="due_date" name="due_date" />
      </label>
    </div>

    <h3>Items</h3>
    <table id="itemsTable">
      <thead><tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Tax %</th><th>Line Total</th><th></th></tr></thead>
      <tbody>
        <tr class="itemRow">
          <td><input name="items[0][description]" required></td>
          <td><input name="items[0][qty]" class="qty" type="number" step="0.01" value="1"></td>
          <td><input name="items[0][unit_price]" class="unit" type="number" step="0.01" value="0"></td>
          <td><input name="items[0][tax_rate]" class="tax" type="number" step="0.01" value="0"></td>
          <td class="lineTotal">0.00</td>
          <td><button type="button" class="removeRow">x</button></td>
        </tr>
      </tbody>
    </table>
    <button type="button" id="addRowBtn">Add Item</button>

    <div id="totals">
      <div>Subtotal: <span id="subtotal">0.00</span></div>
      <div>Tax Total: <span id="tax_total">0.00</span></div>
      <div>Total: <strong id="total">0.00</strong></div>
    </div>

    <div>
      <label>Notes:
        <textarea name="notes"></textarea>
      </label>
    </div>
<div id="addCompanyModal" class="modal hidden">
  <div class="modal-content">
    <h3>Add New Company</h3>
    <form id="addCompanyForm" enctype="multipart/form-data">
      <label>Logo: <input type="file" name="logo"></label><br>
      <label>Company Name: <input type="text" name="name" required></label><br>
      <label>Private Title (for you only): <input type="text" name="title" required></label><br>
      <label>Address: <textarea name="address"></textarea></label><br>
      <label>Phone: <input type="text" name="phone"></label><br>
      <label>Bank Info: <textarea name="bank_info"></textarea></label><br>
      <label>Code Prefix (2 letters): <input type="text" name="prefix" maxlength="2" required></label><br>
      <button type="submit">Save Company</button>
      <button type="button" id="cancelAddCompany">Cancel</button>
    </form>
  </div>
</div>


    <button type="submit">Save & Generate PDF</button>
  </form>

  <script src="assets/app.js"></script>
</body>
</html>
