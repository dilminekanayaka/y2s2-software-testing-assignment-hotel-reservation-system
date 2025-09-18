<?php
require_once 'auth.php';

$result = logoutUser();

// Redirect to home page with success message
header("Location: home.php?message=" . urlencode($result['message']));
exit;
?>
