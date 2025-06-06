<?php
include 'db_config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["amount"], $_POST["invoice_code"], $_POST["created_at"])) {
        $amount = floatval($_POST["amount"]);
        $invoice_code = trim($_POST["invoice_code"]);
        $created_at = $_POST["created_at"];

        $sql = "INSERT INTO cancelled_sales (amount, invoice_code, created_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("dss", $amount, $invoice_code, $created_at);

        if ($stmt->execute()) {
            echo "✅ Cancelled sale recorded successfully!";
        } else {
            echo "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "⚠️ Please fill in all fields.";
    }
}
?>

<!-- Fallback HTML Form -->
<form method="POST">
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="invoice_code" placeholder="Invoice Code" required><br>
    <label for="created_at">Transaction Date</label><br>
    <input type="date" name="created_at" required placeholder="Select transaction date" title="Transaction Date"><br>
    <button type="submit">Submit Cancelled Sale</button>
</form>
