<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/cart.class.php'); 
require_once(__DIR__ . '/../database/wishlist.class.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['item_id'])) {

        $itemId = intval($_POST['item_id']);
        $userId = $session->getId();

        try {
            $lastId = Cart::insertItem($db, $userId, $itemId, 1); 
            
            
          
            
            if ($lastId !== false) {
                $wishlistId = intval($_POST['wishlist_id']);
                Wishlist::deleteItem($db, $wishlistId);

                $session->setMessage("Item successfully added to cart and removed from wishlist.", "success");
                header("Location: /pages");
                exit();
            } else {
                $session->setMessage("Failed to add item to cart.", "error");
                header("Location: /pages");
                exit();
            }
        } catch (Exception $e) {
            // Log the error message
            error_log("Error adding item to cart: " . $e->getMessage());
            $session->setMessage("Error: " . $e->getMessage(), "error");
            header("Location: /pages");
            exit();
        }
    } elseif (isset($_POST['wishlist_id'])) {
        // Handle only removing from wishlist
        $wishlistId = intval($_POST['wishlist_id']);

        try {
            Wishlist::deleteItem($db, $wishlistId);
            $session->setMessage("Item successfully removed from wishlist.", "success");
            header("Location: /pages/wishlist.php");
            exit();
        } catch (Exception $e) {
            // Log the error message
            error_log("Error removing item from wishlist: " . $e->getMessage());
            $session->setMessage("Error: " . $e->getMessage(), "error");
            header("Location: /pages/wishlist.php");
            exit();
        }
    }
} else {
    header("Location: /");
    exit();
}
?>
