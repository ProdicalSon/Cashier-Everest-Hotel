<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $amount = $_POST["amount"];
    $invoice_code = $_POST["invoice_code"];

    $sql = "INSERT INTO paid_bills (name, amount, invoice_code) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sds", $name, $amount, $invoice_code);
    $stmt->execute();

    echo "Paid bill recorded successfully!";
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Customer Name" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="invoice_code" placeholder="Invoice Code" required><br>
    <button type="submit">Submit Paid Bill</button>
</form>
