<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namee = $_POST["namee"];
    $amount = $_POST["amount"];

    $sql = "INSERT INTO expenses (namee, amount) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $namee, $amount);
    $stmt->execute();

    echo "Expense recorded successfully!";
}
?>

<form method="POST">
    <input type="text" name="namee" placeholder="Expense Name" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <button type="submit">Submit Expense</button>
</form>
