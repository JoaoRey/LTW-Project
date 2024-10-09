<?php
declare(strict_types=1);

class Wishlist
{
    public int $wishlistId;
    public int $userId;
    public int $itemId;

    public function __construct(int $wishlistId, int $userId, int $itemId)
    {
        $this->wishlistId = $wishlistId;
        $this->userId = $userId;
        $this->itemId = $itemId;
    }

    static function insertItem(PDO $db, int $userId, int $itemId): string
    {
        try {
            $stmt = $db->prepare('INSERT INTO Wishlist (UserId, ItemId) VALUES (?, ?)');
            $stmt->execute(array($userId, $itemId));
            return $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error inserting item into wishlist: " . $e->getMessage());
        }
    }

    static function deleteItem(PDO $db, int $wishlistId): void
    {
        try {
            $stmt = $db->prepare('DELETE FROM Wishlist WHERE WishlistId = ?');
            $stmt->execute(array($wishlistId));
        } catch (PDOException $e) {
            throw new Exception("Error deleting item from wishlist: " . $e->getMessage());
        }
    }

    static function getItemsByUser(PDO $db, int $userId): array
    {
        try {
            $stmt = $db->prepare('
                SELECT Wishlist.WishlistId, Wishlist.UserId, Wishlist.ItemId 
                FROM Wishlist

                INNER JOIN 
                    Item ON Wishlist.ItemId = Item.ItemId
                WHERE 
                    Wishlist.UserId = ?
            ');
            $stmt->execute(array($userId));

            $items = array();
            while ($item = $stmt->fetch()) {
                $items[] = new Wishlist($item['WishlistId'], $item['UserId'], $item['ItemId']);
            }
            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items from wishlist: " . $e->getMessage());
        }
    }
}
?>
