<?php 
include 'db_config.php';


$reportDate = date("Y-m-d");
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $reportDate = $conn->real_escape_string($_GET['date']);
}
$timestamp = strtotime($reportDate);
$formattedDate = strtoupper(date('jS F, Y', $timestamp));


$transactions = [
    'Mpesa' => ["mpesa", "namee, amount, code, created_at", ['namee', 'amount', 'code', 'created_at']],
    'Paid Bill' => ["paid_bills", "namee, amount, invoice_code AS code, created_at", ['namee', 'amount', 'code', 'created_at']],
    'Unpaid Bill' => ["unpaid_bills", "namee, amount, invoice_code AS code, created_at", ['namee', 'amount', 'code', 'created_at']],
    'Expense' => ["expenses", "namee, amount, created_at", ['namee', 'amount', 'created_at']],
    'Complimentary' => ["complimentary", "invoice_code AS code, amount, created_at", ['code', 'amount', 'created_at']],
    'Cancelled Sale' => ["cancelled_sales", "invoice_code AS code, amount, created_at", ['code', 'amount', 'created_at']]
];

function fetchData($conn, $table, $fields, $reportDate) {
    $query = "SELECT $fields FROM $table WHERE DATE(created_at) = '$reportDate'";
    return $conn->query($query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; }
        .total-row { font-weight: bold; background-color: #e8f5e9; }
        .section { margin-bottom: 40px; }
        form { text-align: center; margin: 20px auto; width: 500px; display:flex; flex-direction: block; align-items: center; }
        input[type="date"] { padding: 8px; margin-right: 20px; }
        input[type="submit"] { padding: 8px; margin-left: 20px; }
        label[for="total_sales"] { margin-right: 10px; }
        input[type="submit"] { padding: 8px 16px; cursor: pointer; background-color: green; color: white; border: none; border-radius: 4px; }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.html">Dashboard</a></li>
            <li><a href="transactiondetails.php">Transactions</a></li>
            <li><a href="generate_report.php">Reports</a></li>
        </ul>
    </nav>

<h2>TRANSACTION REPORT ON <?= $formattedDate ?></h2>

<!-- Date Filter Form -->
<form method="GET" action="transactiondetails.php">
    <input type="date" name="date" id="date" value="<?= htmlspecialchars($reportDate) ?>" required>
    <input type="submit" value="SEARCH">
</form>

<form method="POST" action="generate_report.php" style="text-align:center; margin-top: 20px;">
    <input type="hidden" name="report_date" value="<?= htmlspecialchars($reportDate) ?>">
    <label for="total_sales">ENTER T.S</label>
    <input type="number" step="0.01" name="total_sales" id="total_sales" required>
    <input type="submit" value="Download Report">
</form>


<?php
foreach ($transactions as $label => [$table, $fieldStr, $columns]) {
    $result = fetchData($conn, $table, $fieldStr, $reportDate);
    $sectionTotal = 0;

    echo "<div class='section'>";
    echo "<h3>$label Transactions</h3>";

    if ($result && $result->num_rows > 0) {
        echo "<table><tr>";

        // Table headers
        foreach ($columns as $col) {
            if ($col === 'namee') echo "<th>Name</th>";
            elseif ($col === 'code') echo "<th>Code</th>";
            elseif ($col === 'amount') echo "<th>Amount (KES)</th>";
            elseif ($col === 'created_at') echo "<th>Date</th>";
        }
        echo "</tr>";

        // Table rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($columns as $col) {
                if ($col === 'created_at') {
                    echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row[$col]))) . "</td>";
                } elseif ($col === 'amount') {
                    echo "<td>" . number_format($row[$col], 2) . "</td>";
                    $sectionTotal += $row[$col];
                } else {
                    echo "<td>" . htmlspecialchars($row[$col]) . "</td>";
                }
            }
            echo "</tr>";
        }

        echo "<tr class='total-row'><td colspan='" . (count($columns) - 1) . "'>TOTAL</td><td>KES " . number_format($sectionTotal, 2) . "</td></tr>";
        echo "</table>";
    } else {
        echo "<p style='text-align:center; color:gray;'>No $label transactions recorded on $formattedDate.</p>";
    }
    echo "</div>";
}
?>

</body>
</html>
