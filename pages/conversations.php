<?php
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/conversations.tpl.php');
require_once(__DIR__ . '/../database/communication.class.php'); 

$session = new Session();
if (!$session->isLoggedIn()) {
    header("Location: /pages/login.php");
    exit;
}

$db = getDatabaseConnection();
drawHeader($session, $db);
drawConversations($session, $db); 
drawFooter();
?>