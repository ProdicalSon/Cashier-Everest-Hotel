<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["namee"], $_POST["amount"], $_POST["code"])) {
        $namee = $_POST["namee"];
        $amount = $_POST["amount"];
        $code = $_POST["code"];

        $sql = "INSERT INTO mpesa (namee, amount, code) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sds", $namee, $amount, $code);

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

<form method="POST">
    <input type="text" name="namee" placeholder="Name" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <input type="text" name="code" placeholder="MPESA Code" required><br>
    <button type="submit">Submit MPESA</button>
</form>
