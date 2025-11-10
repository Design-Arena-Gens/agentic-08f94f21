<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action === 'add') {
    $id = (int)($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    if ($id > 0 && $name !== '' && $price >= 0) {
        addToCart($id, $name, $price, $qty);
    }
    header('Location: /cart.php');
    exit;
}
if ($action === 'update') {
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $pid => $q) {
            updateCartQuantity((int)$pid, max(0, (int)$q));
        }
    }
    header('Location: /cart.php');
    exit;
}
if ($action === 'remove') {
    $id = (int)($_GET['product_id'] ?? 0);
    if ($id > 0) removeFromCart($id);
    header('Location: /cart.php');
    exit;
}

$cart = getCart();
$subtotal = cartSubtotal();
?>
<div class="container py-5">
  <h2 class="mb-4">Your Cart</h2>
  <?php if (empty($cart)): ?>
    <div class="alert alert-info">Your cart is empty. <a href="/shop.php">Browse products</a>.</div>
  <?php else: ?>
    <form method="post" action="/cart.php">
      <input type="hidden" name="action" value="update"/>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Product</th>
              <th style="width:120px">Price</th>
              <th style="width:140px">Quantity</th>
              <th style="width:120px">Total</th>
              <th style="width:60px"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $pid => $item): $line = $item['price'] * $item['quantity']; ?>
              <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td>
                  <input type="number" class="form-control" name="quantities[<?php echo (int)$pid; ?>]" value="<?php echo (int)$item['quantity']; ?>" min="0"/>
                </td>
                <td>$<?php echo number_format($line, 2); ?></td>
                <td><a class="btn btn-sm btn-outline-danger" href="/cart.php?action=remove&product_id=<?php echo (int)$pid; ?>">?</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">Subtotal</th>
              <th>$<?php echo number_format($subtotal, 2); ?></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="d-flex justify-content-between">
        <a href="/shop.php" class="btn btn-outline-primary">Continue Shopping</a>
        <div>
          <button type="submit" class="btn btn-secondary me-2">Update Cart</button>
          <a href="/checkout.php" class="btn btn-primary">Checkout</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
