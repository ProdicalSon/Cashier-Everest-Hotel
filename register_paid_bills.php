<?php
include 'db_config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check that the fields exist before using them
    if (isset($_POST["namee"], $_POST["amount"], $_POST["invoice_code"])) {
        $namee = trim($_POST["namee"]);
        $amount = floatval($_POST["amount"]);
        $invoice_code = trim($_POST["invoice_code"]);

        // Make sure your table has the correct columns: namee, amount, invoice_code
        $sql = "INSERT INTO paid_bills (namee, amount, invoice_code) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sds", $namee, $amount, $invoice_code);

        if ($stmt->execute()) {
            echo "✅ Paid bill recorded successfully!";
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
    <input type="text" name="namee" placeholder="Customer Name" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="invoice_code" placeholder="Invoice Code" required><br>
    <label for="created_at">Transaction Date</label><br>
    <input type="date" name="created_at" required placeholder="Select transaction date" title="Transaction Date"><br>
    <button type="submit">Submit Paid Bill</button>
</form>
