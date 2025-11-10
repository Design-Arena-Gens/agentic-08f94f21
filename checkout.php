<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/functions.php';
require __DIR__ . '/config.php';

$cart = getCart();
$subtotal = cartSubtotal();

if (empty($cart)) {
  echo '<div class="container py-5"><div class="alert alert-info">Your cart is empty. <a href="/shop.php">Go to shop</a>.</div></div>';
  include __DIR__ . '/includes/footer.php';
  exit;
}

$customer_name = $_POST['name'] ?? '';
$customer_email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_payment'])) {
    $tx_ref = 'AIPS-' . time() . '-' . random_int(1000, 9999);

    // Save order as initialized
    $sql = 'INSERT INTO orders (tx_ref, customer_name, customer_email, amount, currency, status) VALUES (?, ?, ?, ?, ?, "initialized")';
    if ($stmt = mysqli_prepare($mysqli, $sql)) {
        $currency = 'NGN';
        mysqli_stmt_bind_param($stmt, 'sssds', $tx_ref, $customer_name, $customer_email, $subtotal, $currency);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($mysqli);
        mysqli_stmt_close($stmt);
        // Save items
        foreach ($cart as $pid => $item) {
            $sql2 = 'INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)';
            if ($stmt2 = mysqli_prepare($mysqli, $sql2)) {
                $pid_int = (int)$pid;
                $name = $item['name'];
                $price = (float)$item['price'];
                $qty = (int)$item['quantity'];
                mysqli_stmt_bind_param($stmt2, 'iisdi', $order_id, $pid_int, $name, $price, $qty);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);
            }
        }

        // Store tx_ref in session for success page
        $_SESSION['pending_tx_ref'] = $tx_ref;
    }
}
?>
<div class="container py-5">
  <h2 class="mb-4">Checkout</h2>
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Order Summary</h5>
          <ul class="list-group list-group-flush">
            <?php foreach ($cart as $pid => $item): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold"><?php echo htmlspecialchars($item['name']); ?></div>
                  <div class="small text-muted">Qty: <?php echo (int)$item['quantity']; ?> ? $<?php echo number_format($item['price'], 2); ?></div>
                </div>
                <div class="fw-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div class="fw-semibold">Total</div>
              <div class="fw-bold">$<?php echo number_format($subtotal, 2); ?></div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Customer Details</h5>
          <form method="post" action="/checkout.php">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($customer_name); ?>"/>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($customer_email); ?>"/>
            </div>
            <input type="hidden" name="start_payment" value="1"/>
            <button class="btn btn-primary w-100" type="submit">Proceed to Payment</button>
          </form>
          <?php if (!empty($_SESSION['pending_tx_ref'])): ?>
            <hr/>
            <div class="alert alert-info small">Payment initialized. Click the button below to complete using Flutterwave.</div>
            <button id="payBtn" class="btn btn-success w-100">Pay with Flutterwave</button>
            <script src="https://checkout.flutterwave.com/v3.js"></script>
            <script>
              document.getElementById('payBtn').addEventListener('click', function(){
                FlutterwaveCheckout({
                  public_key: "<?php echo htmlspecialchars($FLW_PUBLIC_KEY); ?>",
                  tx_ref: "<?php echo htmlspecialchars($_SESSION['pending_tx_ref']); ?>",
                  amount: <?php echo json_encode((float)$subtotal); ?>,
                  currency: "NGN",
                  payment_options: "card,account,banktransfer,ussd,mpesa",
                  customer: {
                    email: <?php echo json_encode($customer_email ?: 'customer@example.com'); ?>,
                    name: <?php echo json_encode($customer_name ?: 'AIPS Customer'); ?>
                  },
                  customizations: {
                    title: "AIPS Order",
                    description: "Eco-friendly packaging purchase",
                    logo: "/assets/logo.png"
                  },
                  callback: function (data) {
                    window.location.href = '/success.php?status=' + encodeURIComponent(data.status) + '&tx_ref=' + encodeURIComponent(data.tx_ref) + '&transaction_id=' + encodeURIComponent(data.transaction_id);
                  },
                  onclose: function() {
                    window.location.href = '/cancel.php';
                  }
                });
              });
            </script>
            <div class="small text-muted mt-2">Note: Set your Flutterwave public key in environment variable FLW_PUBLIC_KEY for live/test.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
