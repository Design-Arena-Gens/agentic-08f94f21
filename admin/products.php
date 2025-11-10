<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';
requireAdmin();

$mode = $_POST['mode'] ?? $_GET['mode'] ?? '';

if ($mode === 'create') {
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $image_url = trim($_POST['image_url'] ?? '');
  $category = trim($_POST['category'] ?? '');
  if ($name !== '') {
    $sql = 'INSERT INTO products (name, description, price, image_url, category) VALUES (?, ?, ?, ?, ?)';
    if ($stmt = mysqli_prepare($mysqli, $sql)) {
      mysqli_stmt_bind_param($stmt, 'ssdss', $name, $description, $price, $image_url, $category);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
  }
  header('Location: /admin/products.php');
  exit;
}

if ($mode === 'update') {
  $id = (int)($_POST['id'] ?? 0);
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $image_url = trim($_POST['image_url'] ?? '');
  $category = trim($_POST['category'] ?? '');
  if ($id > 0 && $name !== '') {
    $sql = 'UPDATE products SET name=?, description=?, price=?, image_url=?, category=? WHERE id=?';
    if ($stmt = mysqli_prepare($mysqli, $sql)) {
      mysqli_stmt_bind_param($stmt, 'ssdssi', $name, $description, $price, $image_url, $category, $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
  }
  header('Location: /admin/products.php');
  exit;
}

if ($mode === 'delete') {
  $id = (int)($_GET['id'] ?? 0);
  if ($id > 0) {
    $sql = 'DELETE FROM products WHERE id = ?';
    if ($stmt = mysqli_prepare($mysqli, $sql)) {
      mysqli_stmt_bind_param($stmt, 'i', $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
  }
  header('Location: /admin/products.php');
  exit;
}

// Fetch products for listing
$products = [];
$sql = 'SELECT id, name, description, price, image_url, category FROM products ORDER BY created_at DESC';
if ($stmt = mysqli_prepare($mysqli, $sql)) {
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  while ($row = mysqli_fetch_assoc($result)) $products[] = $row;
  mysqli_stmt_close($stmt);
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Products (Admin)</h2>
    <div>
      <a href="/admin/logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
  </div>
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Add Product</h5>
          <form method="post" action="/admin/products.php">
            <input type="hidden" name="mode" value="create"/>
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input name="name" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Category</label>
              <input name="category" class="form-control" placeholder="Cups / Boxes / Containers"/>
            </div>
            <div class="mb-3">
              <label class="form-label">Price (USD)</label>
              <input type="number" step="0.01" name="price" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Image URL</label>
              <input name="image_url" class="form-control" placeholder="/assets/products/your-image.svg"/>
              <div class="form-text">Use full URL or path starting with /assets/</div>
            </div>
            <button class="btn btn-primary" type="submit">Add</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Product List</h5>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>ID</th><th>Name</th><th>Category</th><th>Price</th><th style="width:140px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $p): ?>
                  <tr>
                    <td><?php echo (int)$p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                    <td>$<?php echo number_format((float)$p['price'], 2); ?></td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit-<?php echo (int)$p['id']; ?>">Edit</button>
                      <a class="btn btn-sm btn-outline-danger" href="/admin/products.php?mode=delete&id=<?php echo (int)$p['id']; ?>">Delete</a>
                    </td>
                  </tr>
                  <tr class="collapse" id="edit-<?php echo (int)$p['id']; ?>">
                    <td colspan="5">
                      <form method="post" action="/admin/products.php" class="border rounded p-3 bg-light">
                        <input type="hidden" name="mode" value="update"/>
                        <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>"/>
                        <div class="row g-2">
                          <div class="col-md-4">
                            <label class="form-label">Name</label>
                            <input name="name" class="form-control" value="<?php echo htmlspecialchars($p['name']); ?>"/>
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <input name="category" class="form-control" value="<?php echo htmlspecialchars($p['category']); ?>"/>
                          </div>
                          <div class="col-md-2">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($p['price']); ?>"/>
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Image URL</label>
                            <input name="image_url" class="form-control" value="<?php echo htmlspecialchars($p['image_url']); ?>"/>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($p['description']); ?></textarea>
                          </div>
                        </div>
                        <div class="mt-2 text-end">
                          <button class="btn btn-primary btn-sm" type="submit">Save</button>
                        </div>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
