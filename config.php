<?php
// Configuration using environment variables with sensible defaults
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'aips_db';

// Flutterwave keys (set in environment for production)
$FLW_PUBLIC_KEY = getenv('FLW_PUBLIC_KEY') ?: '';
$FLW_SECRET_KEY = getenv('FLW_SECRET_KEY') ?: '';
$FLW_ENCRYPTION_KEY = getenv('FLW_ENCRYPTION_KEY') ?: '';

// Admin config
$ADMIN_EMAIL = getenv('ADMIN_EMAIL') ?: 'admin@aips.local';
$ADMIN_PASSWORD_HASH = getenv('ADMIN_PASSWORD_HASH') ?: password_hash('admin123', PASSWORD_BCRYPT);

// Site config
$SITE_NAME = 'All In Packaging Solution (AIPS)';
$SITE_SLOGAN = 'Safety & Clean';
$PRIMARY_GREEN = '#00793B';
$PRIMARY_BLUE = '#1A237E';
?>
