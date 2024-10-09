<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/search.tpl.php');
require_once(__DIR__ . '/../templates/chat.tpl.php');

$db = getDatabaseConnection();

if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}
$userId = $session->getId();


if(isset($_POST['owner_id'], $_POST['item_id'])) {
    $ownerId = isset($_POST['owner_id']) ? intval($_POST['owner_id']) : 0; 
    $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0; 
    
    
    try {
        $communications = Communication::getCommunicationsForItem($db, $userId, $ownerId, $itemId);
    } catch (Exception $e) {
        echo "Error fetching communications: " . $e->getMessage();
        exit;
    }

    // Desenhe o chat com as mensagens obtidas
    drawHeader($session, $db);
    drawChat($db, $userId, $ownerId, $itemId, $communications);
    drawFooter();
} else {
    header('Location: /pages');
    exit;
}
?>