<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/cart.class.php');
require_once(__DIR__ . '/../database/payment.class.php');

function simulatePurchaseAndRedirect() {
    $session = new Session();
    $userId = $session->getId();
    $db = getDatabaseConnection();
    Cart::clearCart($db, $userId);
    $success_message = "Compra realizada com sucesso!";
    $session->addMessage('success', $success_message);
    header('Location: /../pages');
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['checkout_submitted']) && isset($_POST['item_ids'])) {
    

    try {
        $address = $_POST['address'] ?? null;
        $postalCode = $_POST['postal_code'] ?? null;
        $city = $_POST['city'] ?? null;
        $district = $_POST['district'] ?? null;
        $country = $_POST['country'] ?? null;
        
        $session = new Session();
        $userId = $session->getId();
        
        $db = getDatabaseConnection();
        User::updateUserAddress($db, $userId, $address,$postalCode, $city, $district, $country);
        $itemIds = $_POST['item_ids'] ?? null;
        foreach ($itemIds as $itemId) {
            $itemId = intval($itemId);
            Item::updateItemStatus($db, $itemId,false);
            $sellerId = Item::getSellerId($db, $itemId);
            Payment::insertPayment($db, $userId, $sellerId, $itemId, $address, $city, $district, $country, $postalCode, date('Y-m-d H:i:s'));
        }
        simulatePurchaseAndRedirect();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $session = new Session();
    $session->addMessage('error', 'Erro ao finalizar a compra. Por favor, tente novamente.');
    header('Location: /../pages/checkout.php');
    exit();
}
?>
