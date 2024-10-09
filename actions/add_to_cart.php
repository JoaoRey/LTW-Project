<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/cart.class.php'); 

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}

// Verifique se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemId = intval($_POST['item_id']);
    $userId = $session->getId();


    try {
        
        $lastId= Cart::insertItem($db, $userId, $itemId, 1); 
        $session->setMessage("Item successfully added to cart.", "success");
        header("Location: /pages");
        exit();

    } catch (Exception $e) {
        // Defina a mensagem de erro
        $session->setMessage("Error adding item to cart: " . $e->getMessage(), "error");
        header("Location: /");
        exit();
    }
} else {
    header("Location: /");
    exit();
}
?>