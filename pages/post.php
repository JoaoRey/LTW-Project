<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../utils/session.php');
  $session = new Session();
  
  require_once(__DIR__ . '/../database/connection.db.php');
  require_once(__DIR__ . '/../templates/post.tpl.php');
  require_once(__DIR__ . '/../templates/common.tpl.php');
  



  $db = getDatabaseConnection();

  if (!isset($_GET['id']) || empty($_GET['id'])) {
    $session->addMessage('error', 'Invalid Item!');

    header("Location: /pages");
    exit();
}

$itemId = $_GET['id'];
  
  drawHeader($session, $db);
  drawPost($session, $db, (int) $itemId);
  drawFooter();
?>