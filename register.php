<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    $form_type = $_POST['form_type'];
    
    // Use user-provided transaction date; you can add a fallback if needed.
    if (isset($_POST['created_at']) && !empty($_POST['created_at'])) {
        $transaction_date = $_POST['created_at'];
    } else {
        // Optionally fallback to auto-generated timestamp if no date provided.
        $transaction_date = date('Y-m-d');
    }

    switch ($form_type) {
        case 'mpesa':
            $name = $_POST['name'];
            $amount = $_POST['amount'];
            $code = $_POST['code'];
            $stmt = $conn->prepare("INSERT INTO mpesa (name, amount, code, created_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdss", $name, $amount, $code, $transaction_date);
            break;

        case 'paid_bills':
            $name = $_POST['name'];
            $amount = $_POST['amount'];
            $invoice_code = $_POST['invoice_code'];
            $stmt = $conn->prepare("INSERT INTO paid_bills (name, amount, invoice_code, created_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdss", $name, $amount, $invoice_code, $transaction_date);
            break;

        case 'unpaid_bills':
            $name = $_POST['name'];
            $amount = $_POST['amount'];
            $invoice_code = $_POST['invoice_code'];
            $stmt = $conn->prepare("INSERT INTO unpaid_bills (name, amount, invoice_code, created_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdss", $name, $amount, $invoice_code, $transaction_date);
            break;

        case 'expenses':
            $item = $_POST['item'];
            $amount = $_POST['amount'];
            $stmt = $conn->prepare("INSERT INTO expenses (item, amount, created_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $item, $amount, $transaction_date);
            break;

        case 'complimentary':
            $amount = $_POST['amount'];
            $invoice_code = $_POST['invoice_code'];
            $stmt = $conn->prepare("INSERT INTO complimentary (amount, invoice_code, created_at) VALUES (?, ?, ?)");
            $stmt->bind_param("dss", $amount, $invoice_code, $transaction_date);
            break;

        case 'cancelled_sales':
            $amount = $_POST['amount'];
            $invoice_code = $_POST['invoice_code'];
            $stmt = $conn->prepare("INSERT INTO cancelled_sales (amount, invoice_code, created_at) VALUES (?, ?, ?)");
            $stmt->bind_param("dss", $amount, $invoice_code, $transaction_date);
            break;

        default:
            echo "Invalid form type.";
            exit();
    }

    if ($stmt->execute()) {
        header("Location: success.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
