<?php
session_start();
require_once 'db.php';
require_once 'config.php';

// Destroy session
session_unset();
session_destroy();

// Set flash message
session_start();
set_flash('success', 'Anda telah logout. Sampai jumpa lagi!');

// Redirect to homepage
redirect('index.php');
?>
