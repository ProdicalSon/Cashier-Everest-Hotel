<?php
include 'db_config.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["amount"], $_POST["invoice_code"])) {
        $amount = floatval($_POST["amount"]);
        $invoice_code = trim($_POST["invoice_code"]);

        $sql = "INSERT INTO cancelled_sales (amount, invoice_code) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ds", $amount, $invoice_code);

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

<!-- HTML Form -->
<form method="POST">
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="invoice_code" placeholder="Invoice Code" required><br>
    <button type="submit">Submit Cancelled Sale</button>
</form>
