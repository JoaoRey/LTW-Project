<?php
declare(strict_types=1);

class Cart
{
    public int $cartId;
    public int $userId;
    public int $itemId;
    public int $quantity;

    public function __construct(int $cartId, int $userId, int $itemId, int $quantity)
    {
        $this->cartId = $cartId;
        $this->userId = $userId;
        $this->itemId = $itemId;
        $this->quantity = $quantity;
    }
    
    // Insert a new item into the cart
    static function insertItem(PDO $db, int $userId, int $itemId, int $quantity): string
    {
        try {
            $stmt = $db->prepare('
                INSERT INTO Cart (UserId, ItemId, Quantity)
                VALUES (?, ?, ?)
            ');

            // Execute a query com os valores dos parâmetros
            $stmt->execute(array($userId, $itemId, $quantity));

            // Retorne o ID do último registro inserido
            return $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error inserting item into cart: " . $e->getMessage());
        }
    }


    // Update the quantity of an item in the cart
    static function updateItemQuantity(PDO $db, int $cartId, int $quantity): void
    {
        try {
            $stmt = $db->prepare('
                UPDATE Cart
                SET Quantity = ?
                WHERE CartId = ?
            ');

            $stmt->execute(array($quantity, $cartId));
        } catch (PDOException $e) {
            throw new Exception("Error updating item quantity in cart: " . $e->getMessage());
        }
    }

    // Delete an item from the cart
    static function deleteItem(PDO $db, int $cartId): void
    {
        try {
            $stmt = $db->prepare('
                DELETE FROM Cart
                WHERE CartId = ?
            ');

            $stmt->execute(array($cartId));
        } catch (PDOException $e) {
            throw new Exception("Error deleting item from cart: " . $e->getMessage());
        }
    }

    // Get all items in the cart of a user
    static function getItemsByUser(PDO $db, int $userId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT 
                    Cart.CartId, 
                    Cart.UserId, 
                    Cart.ItemId, 
                    Cart.Quantity
                    
                FROM 
                    Cart
                INNER JOIN 
                    Item ON Cart.ItemId = Item.ItemId
                WHERE 
                    Cart.UserId = ?
            ');
    
            $stmt->execute(array($userId));
    
            $items = array();
    
            while ($item = $stmt->fetch()) {
                $items[] = new Cart(
                    $item['CartId'],
                    $item['UserId'],
                    $item['ItemId'],
                    $item['Quantity']
                    
                );
            }
    
            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items from cart: " . $e->getMessage());
        }
    }
    
    //Clear the cart of a user
    static function clearCart(PDO $db, int $userId): void
    {
        try {
            $stmt = $db->prepare('
                DELETE FROM Cart
                WHERE UserId = ?
            ');

            $stmt->execute(array($userId));
        } catch (PDOException $e) {
            throw new Exception("Error clearing cart: " . $e->getMessage());
        }
    }

    // Get the total quantity of items in the cart of a user
    static function getTotalQuantityByUser(PDO $db, int $userId): int
    {
        try {
            $stmt = $db->prepare('
                SELECT SUM(Quantity) as TotalQuantity
                FROM Cart
                WHERE UserId = ?
            ');

            $stmt->execute(array($userId));

            $result = $stmt->fetch();

            return $result['TotalQuantity'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error fetching total quantity from cart: " . $e->getMessage());
        }
    }

    // Save an item to the cart
    static function saveItem(PDO $db, Cart $cart): void {
        try {
            if ($cart->cartId) {
                // Item exists, update it
                self::updateItemQuantity($db, $cart->cartId, $cart->quantity);
            } else {
                // Item does not exist, insert it
                self::insertItem($db, $cart->userId, $cart->itemId, $cart->quantity);
            }
        } catch (PDOException $e) {
            throw new Exception("Error saving item to cart: " . $e->getMessage());
        }
    }
}
?>
