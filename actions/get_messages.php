<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/communication.class.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
$session = new Session();

try {
    $db = getDatabaseConnection();
    
    $ownerId = isset($_GET['owner_id']) ? intval($_GET['owner_id']) : 0;
    $itemId = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
    $senderId = $session->getId();
    
    $messages = Communication::getCommunicationsForItemWithSenderName($db, $senderId, $ownerId, $itemId); // Modificação aqui
    
    header('Content-Type: application/json');

    echo json_encode($messages);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao buscar mensagens: " . $e->getMessage()]);
}
?>
