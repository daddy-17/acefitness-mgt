<?php
require 'config.php';
require 'functions.php';
if (is_logged_in()) {
    log_action($pdo, 'logout', 'User '.$_SESSION['user']['username'].' logged out');
}
session_destroy();
header('Location: index.php');
exit;
?>