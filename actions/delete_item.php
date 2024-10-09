<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["item_id"])) {
        $itemId = intval($_POST["item_id"]);
        
        $userId = $session->getId();
        $sellerId = Item::getSellerId($db, $itemId);
        $admin = User::isAdmin($db, $userId);
        if ($userId === $sellerId || $admin) {
            Item::deleteItem($db, $itemId);
            Item::deleteItemCategories($db, $itemId);
            Item::deleteItemImages($db, $itemId);
            
            $itemDir = __DIR__ . '/../database/uploads/item_' . $itemId;
            if (deleteDirectory($itemDir)) {
                $session->addMessage('success', 'Item and associated files deleted successfully!');
            } else {
                $session->addMessage('warning', 'Item deleted, but failed to delete associated files.');
            }
            
            header('Location: /pages');
            exit();
        } else {
            $session->addMessage('error', 'You are not the seller of this item!');
            header('Location: /pages');
            exit();
        }
    } else {
        $session->addMessage('error', 'Invalid form submission!');
        header('Location: /pages');
        exit();
    }
} else {
    $session->addMessage('error', 'Invalid request method!');
    header('Location: /pages');
    exit();
}
?>
