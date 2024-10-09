<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../utils/session.php');
  $session = new Session();
  
  require_once(__DIR__ . '/../database/connection.db.php');

  $db = getDatabaseConnection();

  require_once(__DIR__ . '/../templates/cart.tpl.php');
  require_once(__DIR__ . '/../templates/common.tpl.php');

  if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}
  drawHeader($session, $db);
  drawCart($session, $db);
  drawFooter();
?>