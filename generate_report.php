<?php
require __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'db_config.php';

// Retrieve and process the report date and total sales from the POST data
$reportDate = $_POST['report_date'];
$totalSales = floatval($_POST['total_sales']);
$formattedDate = strtoupper(date('jS F, Y', strtotime($reportDate)));

// Define the transactions to include in the report.
// Note: For Expense, we include an empty field for code since it doesn't have one.
$transactions = [
    'Mpesa'           => ["mpesa", "namee, amount, code, created_at"],
    'Paid Bill'       => ["paid_bills", "namee, amount, invoice_code AS code, created_at"],
    'Unpaid Bill'     => ["unpaid_bills", "namee, amount, invoice_code AS code, created_at"],
    'Expense'         => ["expenses", "namee, amount, '' AS code, created_at"],
    'Complimentary'   => ["complimentary", "amount, invoice_code AS code, created_at"],
    'Cancelled Sale'  => ["cancelled_sales", "amount, invoice_code AS code, created_at"]
];

// Function to fetch data for a specific table on a given report date.
function fetchData($conn, $table, $fields, $reportDate) {
    $query = "SELECT $fields FROM $table WHERE DATE(created_at) = '$reportDate'";
    return $conn->query($query);
}

// Start building the HTML content for the PDF.
$html = "<h2 style='text-align:center;'>RESTAURANT REPORT ON $formattedDate</h2>";

$totals = [
    'Mpesa'           => 0, 
    'Paid Bill'       => 0, 
    'Unpaid Bill'     => 0, 
    'Expense'         => 0,
    'Complimentary'   => 0, 
    'Cancelled Sale'  => 0
];

// Loop through each transaction type and generate a table.
foreach ($transactions as $label => [$table, $fields]) {
    $result = fetchData($conn, $table, $fields, $reportDate);
    $html .= "<h3>$label Transactions</h3>";

    if ($result && $result->num_rows > 0) {
        // Define a table with appropriate headers depending on type.
        if (in_array($label, ['Complimentary', 'Cancelled Sale'])) {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                        <tr>
                            <th>Amount (KES)</th>
                            <th>Code/Invoice</th>
                            <th>Date</th>
                        </tr>";
        } else {
            $html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                        <tr>
                            <th>Name/Item</th>
                            <th>Amount (KES)</th>
                            <th>Code/Invoice</th>
                            <th>Date</th>
                        </tr>";
        }

        // Process each row and accumulate totals.
        while ($row = $result->fetch_assoc()) {
            $amount = number_format($row['amount'], 2);
            $code = !empty($row['code']) ? $row['code'] : '---';
            $date = date('d-m-Y', strtotime($row['created_at']));

            if (in_array($label, ['Complimentary', 'Cancelled Sale'])) {
                $html .= "<tr>
                            <td>$amount</td>
                            <td>$code</td>
                            <td>$date</td>
                          </tr>";
            } else {
                $name = isset($row['namee']) && !empty($row['namee']) ? $row['namee'] : '---';
                $html .= "<tr>
                            <td>$name</td>
                            <td>$amount</td>
                            <td>$code</td>
                            <td>$date</td>
                          </tr>";
            }

            $totals[$label] += $row['amount'];
        }

        $html .= "</table><br>";
    } else {
        $html .= "<p>No $label transactions recorded on $formattedDate.</p>";
    }
}

// Compute overall totals based on the gathered data.
$totalCancelled   = $totals['Cancelled Sale'];
$totalPaidBills   = $totals['Paid Bill'];
$totalUnpaid      = $totals['Unpaid Bill'];
$totalExpenses    = $totals['Expense'];
$totalComplimentary = $totals['Complimentary'];
$totalMpesa       = $totals['Mpesa'] - $totals['Paid Bill'];

$totalNetSales    = $totalSales - $totalCancelled;
$totalCash        = $totalNetSales - $totalExpenses - $totalComplimentary - $totalUnpaid - $totalMpesa;

// Add Summary Section to the HTML
$html .= "
    <h3>Summary</h3>
    <table border='1' cellpadding='8' cellspacing='0' width='100%'>
        <tr><td><strong>Total Sales</strong></td><td>KES " . number_format($totalSales, 2) . "</td></tr>
        <tr><td><strong>Total Cancelled</strong></td><td>KES " . number_format($totalCancelled, 2) . "</td></tr>
        <tr><td><strong>Total Net Sales</strong></td><td>KES " . number_format($totalNetSales, 2) . "</td></tr>
        <tr><td><strong>Total Paid Bills</strong></td><td>KES " . number_format($totalPaidBills, 2) . "</td></tr>
        <tr><td><strong>Total Unpaid Bills</strong></td><td>KES " . number_format($totalUnpaid, 2) . "</td></tr>
        <tr><td><strong>Total Expenses</strong></td><td>KES " . number_format($totalExpenses, 2) . "</td></tr>
        <tr><td><strong>Total Complimentary</strong></td><td>KES " . number_format($totalComplimentary, 2) . "</td></tr>
        <tr><td><strong>Total Mpesa (Net)</strong></td><td>KES " . number_format($totalMpesa, 2) . "</td></tr>
        <tr><td><strong>Total Cash (Balance)</strong></td><td>KES " . number_format($totalCash, 2) . "</td></tr>
    </table>
";

// Configure Dompdf options and instantiate a new instance
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Stream the generated PDF back to the browser. Attachment:true forces a download.
$dompdf->stream("Restaurant_Report_$reportDate.pdf", ["Attachment" => true]);
exit;
?>
