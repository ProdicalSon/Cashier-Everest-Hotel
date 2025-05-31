<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
  if (isset($_POST["namee"], $_POST["amount"], $_POST["invoice_code"])) {
        $name = $_POST["namee"];
        $amount = floatval($_POST["amount"]);
        $invoice_code = $_POST["invoice_code"];

    $sql = "INSERT INTO unpaid_bills (namee, amount, invoice_code) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sds", $name, $amount, $invoice_code);
    $stmt->execute();

    echo "Unpaid bill recorded successfully!";
  }
}
?>

<form method="POST">
    <input type="text" name="namee" placeholder="Customer Name" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="invoice_code" placeholder="Invoice Code" required><br>
    <button type="submit">Submit Unpaid Bill</button>
</form>
