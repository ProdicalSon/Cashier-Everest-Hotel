<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["namee"], $_POST["amount"], $_POST["created_at"])) {
        $namee     = trim($_POST["namee"]);
        $amount    = floatval($_POST["amount"]);
        $created_at = $_POST["created_at"];

        // Ensure that your expenses table has a 'created_at' column (e.g., DATE type)
        $sql = "INSERT INTO expenses (namee, amount, created_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sds", $namee, $amount, $created_at);
        
        if ($stmt->execute()) {
            echo "Expense recorded successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<!-- Fallback HTML Form -->
<form method="POST">
    <input type="text" name="namee" placeholder="Expense Name" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <label for="created_at">Transaction Date</label><br>
    <input type="date" name="created_at" required placeholder="Select transaction date" title="Transaction Date"><br>
    <button type="submit">Submit Expense</button>
</form>
