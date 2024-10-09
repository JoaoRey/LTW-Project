<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../utils/session.php');
  $session = new Session();
  
  require_once(__DIR__ . '/../database/connection.db.php');

  $db = getDatabaseConnection();

  require_once(__DIR__ . '/../templates/checkout.tpl.php');
  require_once(__DIR__ . '/../templates/common.tpl.php');

  if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['item_ids']) && is_array($_POST['item_ids'])) {
        // IDs dos itens estão disponíveis em $_POST['item_ids']
        $itemIds = $_POST['item_ids'];

        // Agora você pode usar os IDs dos itens para obter suas informações do banco de dados, se necessário
        foreach ($itemIds as $itemId) {
          $itemId = intval($itemId);
          $item = Item::getItem($db, $itemId);
            
        }
    }
}
$userId= $session->getId();



  drawHeader($session, $db);
  drawCheckout($session, $db ,$item, $userId);
  drawFooter();
?>