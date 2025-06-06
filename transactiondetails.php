<?php 
include 'db_config.php';

// Default to today's date if no date is provided via GET
$reportDate = date("Y-m-d");
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $reportDate = $conn->real_escape_string($_GET['date']);
}

// Format the report date nicely for display
$timestamp = strtotime($reportDate);
$formattedDate = strtoupper(date('jS F, Y', $timestamp));

// Define the transactions to fetch, each with its table name, SQL field list, and a list of columns for rendering
$transactions = [
    'Mpesa' => ["mpesa", "namee, amount, code, created_at", ['namee', 'amount', 'code', 'created_at']],
    'Paid Bill' => ["paid_bills", "namee, amount, invoice_code AS code, created_at", ['namee', 'amount', 'code', 'created_at']],
    'Unpaid Bill' => ["unpaid_bills", "namee, amount, invoice_code AS code, created_at", ['namee', 'amount', 'code', 'created_at']],
    'Expense' => ["expenses", "namee, amount, created_at", ['namee', 'amount', 'created_at']],
    'Complimentary' => ["complimentary", "invoice_code AS code, amount, created_at", ['code', 'amount', 'created_at']],
    'Cancelled Sale' => ["cancelled_sales", "invoice_code AS code, amount, created_at", ['code', 'amount', 'created_at']]
];

// Function to fetch data from a given table for the selected date
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
        form { text-align: center; margin: 20px auto; width: 500px; display: flex; flex-direction: column; align-items: center; }
        input[type="date"] { padding: 8px; margin-right: 20px; }
        input[type="submit"] { padding: 8px 16px; cursor: pointer; background-color: green; color: white; border: none; border-radius: 4px; }
        label[for="total_sales"] { margin-right: 10px; }
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

    <!-- Display the report date in a friendly format -->
    <h2>TRANSACTION REPORT ON <?= $formattedDate ?></h2>

    <!-- Date Filter Form: Users can change the date, and the page will reload with the selected date's transactions -->
    <form method="GET" action="transactiondetails.php">
        <input type="date" name="date" id="date" value="<?= htmlspecialchars($reportDate) ?>" required>
        <input type="submit" value="SEARCH">
    </form>

    <!-- Report Download Form -->
    <form method="POST" action="generate_report.php" style="text-align:center; margin-top: 20px;">
        <input type="hidden" name="report_date" value="<?= htmlspecialchars($reportDate) ?>">
        <label for="total_sales">ENTER T.S</label>
        <input type="number" step="0.01" name="total_sales" id="total_sales" required>
        <input type="submit" value="Download Report">
    </form>

    <?php
    // Loop through each transaction category and render the data table
    foreach ($transactions as $label => [$table, $fieldStr, $columns]) {
        $result = fetchData($conn, $table, $fieldStr, $reportDate);
        $sectionTotal = 0;

        echo "<div class='section'>";
        echo "<h3>$label Transactions</h3>";

        if ($result && $result->num_rows > 0) {
            echo "<table><tr>";

            // Print table headers based on columns
            foreach ($columns as $col) {
                if ($col === 'namee') echo "<th>Name</th>";
                elseif ($col === 'code') echo "<th>Code</th>";
                elseif ($col === 'amount') echo "<th>Amount (KES)</th>";
                elseif ($col === 'created_at') echo "<th>Date</th>";
            }
            echo "</tr>";

            // Render each data row from the query result
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $col) {
                    if ($col === 'created_at') {
                        // Format the date from YYYY-MM-DD to dd-mm-YYYY format
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

            // Render the total row for this transaction type
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
