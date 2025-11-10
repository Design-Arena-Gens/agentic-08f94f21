<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/db.php';

$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name === '' || $email === '' || $message === '') {
        $error = 'All fields are required.';
    } else {
        $sql = 'INSERT INTO messages (name, email, message) VALUES (?, ?, ?)';
        if ($stmt = mysqli_prepare($mysqli, $sql)) {
            mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $message);
            if (mysqli_stmt_execute($stmt)) {
                $success = true;
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-6">
      <h2>Contact Us</h2>
      <p class="text-muted">Have a question about eco-friendly packaging or bulk orders? Send us a message.</p>
      <?php if ($success): ?>
        <div class="alert alert-success">Thanks! Your message has been received.</div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="post" action="/contact.php">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Send</button>
      </form>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Company Details</h5>
          <ul class="list-unstyled">
            <li><strong>Company:</strong> All In Packaging Solution (AIPS)</li>
            <li><strong>Slogan:</strong> Safety &amp; Clean</li>
            <li><strong>Email:</strong> info@aips.example</li>
            <li><strong>Phone:</strong> +1 (555) 010-1234</li>
            <li><strong>Address:</strong> 100 Eco Avenue, Green City</li>
          </ul>
          <div class="small text-muted">We respond within 1-2 business days.</div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
