<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/functions.php';

// Fetch products
$products = [];
$sql = 'SELECT id, name, description, price, image_url, category FROM products ORDER BY created_at DESC';
if ($stmt = mysqli_prepare($mysqli, $sql)) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Shop</h2>
    <a href="/cart.php" class="btn btn-outline-primary">View Cart</a>
  </div>
  <?php if (empty($products)): ?>
    <div class="alert alert-info">No products found. Please add products in the Admin dashboard.</div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($products as $p): ?>
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" onerror="this.src='/assets/products/placeholder.svg'" class="card-img-top p-3" alt="<?php echo htmlspecialchars($p['name']); ?>"/>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1"><?php echo htmlspecialchars($p['name']); ?></h5>
              <div class="text-muted small mb-2"><?php echo htmlspecialchars($p['category']); ?></div>
              <p class="card-text flex-grow-1"><?php echo htmlspecialchars($p['description']); ?></p>
              <div class="d-flex align-items-center justify-content-between mt-2">
                <div class="fw-bold">$<?php echo number_format((float)$p['price'], 2); ?></div>
                <form method="post" action="/cart.php" class="d-flex align-items-center">
                  <input type="hidden" name="action" value="add"/>
                  <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>"/>
                  <input type="hidden" name="name" value="<?php echo htmlspecialchars($p['name']); ?>"/>
                  <input type="hidden" name="price" value="<?php echo htmlspecialchars($p['price']); ?>"/>
                  <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm me-2" style="width:80px"/>
                  <button class="btn btn-primary btn-sm" type="submit">Add</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
