<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/wishlist.class.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $wishlistId = intval($_POST['wishlist_id']);

    try {
        Wishlist::deleteItem($db, $wishlistId);
        $session->setMessage("Item successfully removed from wishlist.", "success");
        header("Location: /pages/wishlist.php");
        exit();
    } catch (Exception $e) {
        $session->setMessage("Error removing item from wishlist: " . $e->getMessage(), "error");
        header("Location: /pages/wishlist.php");
        exit();
    }
} else {
    header("Location: /");
    exit();
}
?>
