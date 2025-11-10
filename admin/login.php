<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';

if (isAdminLoggedIn()) {
  header('Location: /admin/products.php');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // Try DB admin first
  $sql = 'SELECT password_hash FROM admins WHERE email = ? LIMIT 1';
  if ($stmt = mysqli_prepare($mysqli, $sql)) {
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
      if (password_verify($password, $row['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        header('Location: /admin/products.php');
        exit;
      }
    }
    mysqli_stmt_close($stmt);
  }

  // Fallback to config admin
  if ($email === $ADMIN_EMAIL && password_verify($password, $ADMIN_PASSWORD_HASH)) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_email'] = $email;
    header('Location: /admin/products.php');
    exit;
  } else {
    $error = 'Invalid credentials.';
  }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5" style="max-width:560px;">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Admin Login</h4>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post" action="/admin/login.php">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($ADMIN_EMAIL); ?>"/>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Sign In</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
