<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST["amount"];
    $invoice_code = $_POST["invoice_code"];

    $sql = "INSERT INTO complimentary (amount, invoice_code) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ds", $amount, $invoice_code);
    $stmt->execute();

    echo "Complimentary recorded successfully!";
}
?>

<form method="POST">
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="invoice_code" placeholder="Invoice Code" required><br>
    <button type="submit">Submit Complimentary</button>
</form>
