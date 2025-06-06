<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["namee"], $_POST["amount"], $_POST["code"], $_POST["created_at"])) {
        $namee      = $_POST["namee"];
        $amount     = $_POST["amount"];
        $code       = $_POST["code"];
        $created_at = $_POST["created_at"];

        $sql = "INSERT INTO mpesa (namee, amount, code, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdss", $namee, $amount, $code, $created_at);

        if ($stmt->execute()) {
            echo "Mpesa transaction recorded successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<!-- Fallback HTML form in case the file is accessed directly -->
<form method="POST">
    <input type="text" name="namee" placeholder="Name" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="code" placeholder="MPESA Code" required><br>
    <label for="created_at">Transaction Date</label><br>
    <input type="date" name="created_at" required placeholder="Select transaction date" title="Transaction Date"><br>
    <button type="submit">Submit MPESA</button>
</form>
