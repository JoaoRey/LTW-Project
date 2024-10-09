<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/wishlist.class.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemId = intval($_POST['item_id']);
    $userId = $session->getId();

    try {
        $lastId = Wishlist::insertItem($db, $userId, $itemId);
        $session->setMessage("Item successfully added to wishlist.", "success");
        header("Location: /pages");
        exit();
    } catch (Exception $e) {
        $session->setMessage("Error adding item to wishlist: " . $e->getMessage(), "error");
        header("Location: /");
        exit();
    }
} else {
    header("Location: /");
    exit();
}
?>
