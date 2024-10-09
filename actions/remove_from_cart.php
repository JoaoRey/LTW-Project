<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/cart.class.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();


if(isset($_POST['cart_id'])) {
    $cartId = (int)$_POST['cart_id'];

    try {
        Cart::deleteItem($db, $cartId);

        header('Location: ../pages/cart.php');
        exit();
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: ../pages/cart.php');
    exit();
}
?>
