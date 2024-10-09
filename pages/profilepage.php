<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
$session = new Session();
session_start();

if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}


if (isset($_SESSION['redirect_to_profile']) && $_SESSION['redirect_to_profile']) {
    unset($_SESSION['redirect_to_profile']); 
    header('Location: ../pages/profilepage.php'); 
    exit();
}

require_once(__DIR__ . '/../templates/profile.tpl.php');
require_once(__DIR__ . '/../templates/common.tpl.php');

$db = getDatabaseConnection();

drawHeader($session, $db);
drawProfile($session, $db);
drawFooter();
?>
