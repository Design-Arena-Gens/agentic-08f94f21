<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/db.php';
$status = $_GET['status'] ?? '';
$tx_ref = $_GET['tx_ref'] ?? '';
$transaction_id = $_GET['transaction_id'] ?? '';

// Mark order as paid if present
if ($tx_ref !== '') {
    $sql = 'UPDATE orders SET status = "paid" WHERE tx_ref = ?';
    if ($stmt = mysqli_prepare($mysqli, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $tx_ref);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    // Empty cart
    if (isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
    unset($_SESSION['pending_tx_ref']);
}
?>
<div class="container py-5">
  <div class="alert alert-success">
    <h4 class="alert-heading">Payment Successful</h4>
    <p>Thank you! Your payment was processed. Reference: <strong><?php echo htmlspecialchars($tx_ref); ?></strong></p>
    <hr>
    <a href="/shop.php" class="btn btn-primary">Continue Shopping</a>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
