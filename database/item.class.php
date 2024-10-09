<?php
declare(strict_types=1);

class Category {
    public int $categoryId;
    public string $categoryName;

    public function __construct(int $categoryId, string $categoryName) {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
    }
}

class Brand {
    public int $brandId;
    public string $brandName;

    public function __construct(int $brandId, string $brandName) {
        $this->brandId = $brandId;
        $this->brandName = $brandName;
    }
}

class Condition {
    public int $conditionId;
    public string $conditionName;

    public function __construct(int $conditionId, string $conditionName) {
        $this->conditionId = $conditionId;
        $this->conditionName = $conditionName;
    }
}

class Size {
    public int $sizeId;
    public string $sizeName;

    public function __construct(int $sizeId, string $sizeName) {
        $this->sizeId = $sizeId;
        $this->sizeName = $sizeName;
    }
}

class Model {
    public int $modelId;
    public string $modelName;

    public function __construct(int $modelId, string $modelName) {
        $this->modelId = $modelId;
        $this->modelName = $modelName;
    }
}

class Image {
    public int $imageId;
    public string $imageUrl;

    public function __construct(int $imageId, string $imageUrl) {
        $this->imageId = $imageId;
        $this->imageUrl = $imageUrl;
    }
}

class Item {
    public int $itemId;
    public int $sellerId;
    public string $title;
    public string $description;
    public float $price;
    public string $listingDate;
    public bool $active;

    public function __construct(int $itemId, int $sellerId, string $title, string $description, float $price, string $listingDate, bool $active) {
        $this->itemId = $itemId;
        $this->sellerId = $sellerId;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->listingDate = $listingDate;
        $this->active = $active;
    }

    //Update item Status
    static function updateItemStatus(PDO $db, int $itemId, bool $active) {
        try {
            $stmt = $db->prepare('UPDATE Item SET Active = ? WHERE ItemId = ?');
            $stmt->execute([$active, $itemId]);
        } catch (PDOException $e) {
            throw new Exception("Error updating item status: " . $e->getMessage());
        }
    }


    // Get item by Id
    static function getItem(PDO $db, int $id) : ?Item {
        try {
            $stmt = $db->prepare('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                WHERE ItemId = ?
            ');
            $stmt->execute(array($id));
    
            $item = $stmt->fetch();
    
            if (!$item) {
                return null;
            }
            $active = (bool) $item['Active'];
            return new Item(
                $item['ItemId'],
                $item['SellerId'],
                $item['Title'],
                $item['Description'],
                $item['Price'],
                $item['ListingDate'],
                $active
            );
        } catch (PDOException $e) {
            throw new Exception("Error fetching item: " . $e->getMessage());
        }
    }
    
    
    
    static function getItems(PDO $db, int $limit) : array {
        try {
            $stmt = $db->prepare('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                LIMIT ?
            ');
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $items = array();

            while ($item = $stmt->fetch()) {
                if ($item['Active']) {
                    $active = (bool) $item['Active'];
                    $items[] = new Item(
                        $item['ItemId'],
                        $item['SellerId'],
                        $item['Title'],
                        $item['Description'],
                        $item['Price'],
                        $item['ListingDate'],
                        $active
                    );
                }
            }
                
            

            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items: " . $e->getMessage());
        }
    }


    // Search Items
    static function searchItems(PDO $db, string $search, int $count) : array {
        try {
            $stmt = $db->prepare('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                WHERE Title LIKE ? AND Active = 1
                LIMIT ?
            ');
            $stmt->bindValue(1, '%' . $search . '%', PDO::PARAM_STR);
            $stmt->bindValue(2, $count, PDO::PARAM_INT);
            $stmt->execute();
            $items = array();

            while ($item = $stmt->fetch()) {
                $active = (bool) $item['Active'];
                $items[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }

            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items by title: " . $e->getMessage());
        }
    }

    // Get items by category name
    static function getItemsByCategoryName(PDO $db, int $limit, string $categoryName): array {
        try {
            $stmt = $db->prepare('
                SELECT Item.ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                JOIN ItemCategory ON Item.ItemId = ItemCategory.ItemId
                JOIN ProductCategory ON ItemCategory.CategoryId = ProductCategory.CategoryId
                WHERE ProductCategory.CategoryName = ? AND Active = 1
                LIMIT ?
            ');
            $stmt->bindValue(1, $categoryName, PDO::PARAM_STR);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $items = array();

            while ($item = $stmt->fetch()) {
                $active = (bool) $item['Active'];   
                $items[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }

            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items by category: " . $e->getMessage());
        }
    }
    //get category id
    static function getCategoryId(PDO $db, ?string $categoryName) : int {
        if($categoryName==null){return 0;}
        try {
            $stmt = $db->prepare('SELECT CategoryId FROM ProductCategory WHERE CategoryName = ?');
            $stmt->execute([$categoryName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['CategoryId'];
        } catch (PDOException $e) {
            throw new Exception("Error fetching category id: " . $e->getMessage());
        }
    }

    //get condition id
    static function getConditionId(PDO $db, ?string $conditionName) : int {
        if ($conditionName == null) {return 0;}
        try {
            $stmt = $db->prepare('SELECT ConditionId FROM ItemCondition WHERE ConditionName = ?');
            $stmt->execute([$conditionName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['ConditionId'];
        } catch (PDOException $e) {
            throw new Exception("Error fetching condition id: " . $e->getMessage());
        }
    }

    //get brand id
    static function getBrandId(PDO $db, ?string $brandName) : int {
        if ($brandName == null) {return 0;}
        try {
            $stmt = $db->prepare('SELECT BrandId FROM ItemBrand WHERE BrandName = ?');
            $stmt->execute([$brandName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['BrandId'];
        } catch (PDOException $e) {
            throw new Exception("Error fetching brand id: " . $e->getMessage());
        }
    }

    //get size id
    static function getSizeId(PDO $db, ?string $sizeName) : int {
        if ($sizeName == null) {return 0;}
        try {
            $stmt = $db->prepare('SELECT SizeId FROM ItemSize WHERE SizeName = ?');
            $stmt->execute([$sizeName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['SizeId'];
        } catch (PDOException $e) {
            throw new Exception("Error fetching size id: " . $e->getMessage());
        }
    }

    //get model id
    static function getModelId(PDO $db, ?string $modelName) : int {
        if ($modelName == null) {return 0;}
        try {
            $stmt = $db->prepare('SELECT ModelId FROM ItemModel WHERE ModelName = ?');
            $stmt->execute([$modelName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['ModelId'];
        } catch (PDOException $e) {
            throw new Exception("Error fetching model id: " . $e->getMessage());
        }
    }

    
    

    // Filter items by category id
    public static function filterItemsByCategoryId($db, ?int $categoryId = 0, array $items = []) {
        try {
            $query = "SELECT Item.ItemId, SellerId, Title, Description, Price, ListingDate, Active
                      FROM Item
                      JOIN ItemCategory ON Item.ItemId = ItemCategory.ItemId
                      WHERE 1=1";
            
            $params = [];
    
            if ($categoryId !== 0) {
                $query .= " AND ItemCategory.CategoryId = ?";
                $params[] = $categoryId;
            }
    
        
            if (empty($items)) {
                $query .= " AND Item.Active = 1"; 
            } else {
                $itemIds = array_map(function($item) {
                    return $item->itemId;
                }, $items);
    
                $query .= " AND Item.ItemId IN (" . implode(',', $itemIds) . ")";
            }
    
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $filteredItems = [];
    
            while ($item = $stmt->fetch()) {
                $active = (bool)$item['Active'];
                $filteredItems[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }
    
            return $filteredItems;
        } catch (PDOException $e) {
            throw new Exception("Error filtering items by category id: " . $e->getMessage());
        }
    }

    //get sellerId by ItemId
    static function getSellerId(PDO $db, int $itemId) : int {
        try {
            $stmt = $db->prepare('SELECT SellerId FROM Item WHERE ItemId = ?');
            $stmt->execute([$itemId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['SellerId'];
        } catch (PDOException $e) {
            throw new Exception("Error fetching seller id: " . $e->getMessage());
        }
    }
    //get all items by userid that are active
    static function getActiveItemsBySellerId(PDO $db, int $sellerId) : array {
        try {
            $stmt = $db->prepare('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                WHERE SellerId = ? AND Active = 1
            ');
            $stmt->execute([$sellerId]);
            $items = array();

            while ($item = $stmt->fetch()) {
                $active = (bool) $item['Active'];
                $items[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }

            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items by seller id: " . $e->getMessage());
        }
    }

    //get all items by userid that are active
    static function getInactiveItemsBySellerId(PDO $db, int $sellerId) : array {
        try {
            $stmt = $db->prepare('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                WHERE SellerId = ? AND Active = 0
            ');
            $stmt->execute([$sellerId]);
            $items = array();

            while ($item = $stmt->fetch()) {
                $active = (bool) $item['Active'];
                $items[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }

            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items by seller id: " . $e->getMessage());
        }
    }



    //get all inactive items
    public static function getInactiveItems(PDO $db) : array {
        try {
            $stmt = $db->query('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                WHERE Active = FALSE
            ');
            $items = array();

            while ($item = $stmt->fetch()) {
                $active = (bool) $item['Active'];
                $items[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }

            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching inactive items: " . $e->getMessage());
        }
    }



    // Filter items by brand id
    public static function filterItemsByBrandId($db, ?int $brandId = 0, array $items = []) {
        try {
            $query = "SELECT Item.ItemId, SellerId, Title, Description, Price, ListingDate, Active
                      FROM Item
                      WHERE 1=1";
            
            $params = [];
    
            if ($brandId !== 0) {
                $query .= " AND BrandId = ?";
                $params[] = $brandId;
            }
    
            if (empty($items)) {
                $query .= " AND Item.Active = 1"; 
            } else {
                $itemIds = array_map(function($item) {
                    return $item->itemId;
                }, $items);
    
                $query .= " AND Item.ItemId IN (" . implode(',', $itemIds) . ")";
            }
    
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $filteredItems = [];
    
            while ($item = $stmt->fetch()) {
                $active = (bool)$item['Active'];
                $filteredItems[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }
    
            return $filteredItems;
        } catch (PDOException $e) {
            throw new Exception("Error filtering items by brand id: " . $e->getMessage());
        }
    }
   

    
    
    
    // Filter items by condition id
    public static function filterItemsByConditionId($db, ?int $conditionId = 0, array $items = []) {
        try {
            $query = "SELECT Item.ItemId, SellerId, Title, Description, Price, ListingDate, Active
                      FROM Item
                      WHERE 1=1";
            
            $params = [];
    
            if ($conditionId !== 0) {
                $query .= " AND ConditionId = ?";
                $params[] = $conditionId;
            }
    
            if (empty($items)) {
                $query .= " AND Item.Active = 1"; 
            } else {
                $itemIds = array_map(function($item) {
                    return $item->itemId;
                }, $items);
    
                $query .= " AND Item.ItemId IN (" . implode(',', $itemIds) . ")";
            }
    
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $filteredItems = [];
    
            while ($item = $stmt->fetch()) {
                $active = (bool)$item['Active'];
                $filteredItems[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }
    
            return $filteredItems;
        } catch (PDOException $e) {
            throw new Exception("Error filtering items by condition id: " . $e->getMessage());
        }
    }

    // Filter items by size id
    public static function filterItemsBySizeId($db, ?int $sizeId = 0, array $items = []) {
        try {
            $query = "SELECT Item.ItemId, SellerId, Title, Description, Price, ListingDate, Active
                      FROM Item
                      WHERE 1=1";
            
            $params = [];
    
            if ($sizeId !== 0) {
                $query .= " AND SizeId = ?";
                $params[] = $sizeId;
            }
    
            if (empty($items)) {
                $query .= " AND Item.Active = 1"; 
            } else {
                $itemIds = array_map(function($item) {
                    return $item->itemId;
                }, $items);
    
                $query .= " AND Item.ItemId IN (" . implode(',', $itemIds) . ")";
            }
    
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $filteredItems = [];
    
            while ($item = $stmt->fetch()) {
                $active = (bool)$item['Active'];
                $filteredItems[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }
    
            return $filteredItems;
        } catch (PDOException $e) {
            throw new Exception("Error filtering items by size id: " . $e->getMessage());
        }
    }

    // Filter items by model id
    public static function filterItemsByModelId($db, ?int $modelId = 0, array $items = []) {
        try {
            $query = "SELECT Item.ItemId, SellerId, Title, Description, Price, ListingDate, Active
                      FROM Item
                      WHERE 1=1";
            
            $params = [];
    
            if ($modelId !== 0) {
                $query .= " AND ModelId = ?";
                $params[] = $modelId;
            }
    
            if (empty($items)) {
                $query .= " AND Item.Active = 1"; 
            } else {
                $itemIds = array_map(function($item) {
                    return $item->itemId;
                }, $items);
    
                $query .= " AND Item.ItemId IN (" . implode(',', $itemIds) . ")";
            }
    
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $filteredItems = [];
    
            while ($item = $stmt->fetch()) {
                $active = (bool)$item['Active'];
                $filteredItems[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }
    
            return $filteredItems;
        } catch (PDOException $e) {
            throw new Exception("Error filtering items by model id: " . $e->getMessage());
        }
    }
    
    
    
    

    
    
    




    //Get item name by ID
    static function getItemNameById(PDO $db, int $id) : string {
        try {
            $stmt = $db->prepare('SELECT Title FROM Item WHERE ItemId = ?');
            $stmt->execute(array($id));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['Title'];
        } catch (PDOException $e) {
            throw new Exception("Error fetching item name: " . $e->getMessage());
        }
    }
    static function getItemsbyName(PDO $db, string $get , int $limit): array{
        try {
            $stmt = $db->prepare('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                WHERE Title LIKE ? 
                LIMIT ?
            ');
            $stmt->bindValue(1, '%' . $get . '%', PDO::PARAM_STR);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $items = array();
    
            while ($item = $stmt->fetch()) {
                $active = (bool) $item['Active'];
                $items[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }
    
            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items by name: " . $e->getMessage());
        }
    }
    
    //get all items by userid
    static function getItemsBySellerId(PDO $db, int $sellerId) : array {
        try {
            $stmt = $db->prepare('
                SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
                FROM Item
                WHERE SellerId = ?
            ');
            $stmt->execute([$sellerId]);
            $items = array();
    
            while ($item = $stmt->fetch()) {
                $active = (bool) $item['Active'];
                $items[] = new Item(
                    $item['ItemId'],
                    $item['SellerId'],
                    $item['Title'],
                    $item['Description'],
                    $item['Price'],
                    $item['ListingDate'],
                    $active
                );
            }
    
            return $items;
        } catch (PDOException $e) {
            throw new Exception("Error fetching items by seller id: " . $e->getMessage());
        }
    }
    
    
      

    // Get total products count
    static function getCountbyCategory($db, ?string $categoryName = null) {
        try {
            if ($categoryName) {
                $stmt = $db->prepare('SELECT COUNT(*) AS total FROM Item 
                                      JOIN ItemCategory ON Item.ItemId = ItemCategory.ItemId
                                      JOIN ProductCategory ON ItemCategory.CategoryId = ProductCategory.CategoryId
                                      WHERE ProductCategory.CategoryName = ?
                                      ');
                $stmt->execute([$categoryName]);
            } else {
                $stmt = $db->query('SELECT COUNT(*) AS total FROM Item');
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return 0; 
        }
    }
    

    // Get all categories of an item
    static function getCategoriesByItemId(PDO $db, int $itemId) : array {
        try {
            $stmt = $db->prepare('
                SELECT ProductCategory.CategoryId, ProductCategory.CategoryName
                FROM ProductCategory 
                JOIN ItemCategory ON ProductCategory.CategoryId = ItemCategory.CategoryId
                WHERE ItemCategory.ItemId = ?
            ');
            $stmt->execute([$itemId]);
            $categories = array();

            while ($category = $stmt->fetch()) {
                $categories[] = new Category($category['CategoryId'], $category['CategoryName']);
            }

            return $categories;
        } catch (PDOException $e) {
            throw new Exception("Error fetching item categories: " . $e->getMessage());
        }
    }
    

    // Get item brand
public static function getItemBrand(PDO $db, int $id) : ?Brand {
    try {
        $stmt = $db->prepare('
            SELECT ItemBrand.BrandId, BrandName
            FROM ItemBrand 
            JOIN Item ON ItemBrand.BrandId = Item.BrandId
            WHERE Item.ItemId = ?
            
        ');
        $stmt->execute(array($id));
        $brand = $stmt->fetch();

        if ($brand) {
            return new Brand($brand['BrandId'], $brand['BrandName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching item brands: " . $e->getMessage());
    }
}

// Get item condition
static function getItemCondition(PDO $db, int $id) : ?Condition {
    try {
        $stmt = $db->prepare('
            SELECT ItemCondition.ConditionId, ConditionName
            FROM ItemCondition 
            JOIN Item ON ItemCondition.ConditionId = Item.ConditionId
            WHERE Item.ItemId = ?
        ');
        $stmt->execute(array($id));
        $condition = $stmt->fetch();

        if ($condition) {
            return new Condition($condition['ConditionId'], $condition['ConditionName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching item conditions: " . $e->getMessage());
    }
}

public static function getItemBrandByName(PDO $db, string $brandname) : ?Brand {
    try {
        $stmt = $db->prepare('
            SELECT BrandId, BrandName
            FROM ItemBrand 
            WHERE BrandName = ?
        ');
        $stmt->execute([$brandname]);
        $brand = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($brand) {
            return new Brand($brand['BrandId'], $brand['BrandName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching item brands: " . $e->getMessage());
    }
}

public static function getItemConditionByName(PDO $db, string $conditionname) : ?Condition {
    try {
        $stmt = $db->prepare('
            SELECT ConditionId, ConditionName
            FROM ItemCondition 
            WHERE ConditionName = ?
        ');
        $stmt->execute([$conditionname]);
        $condition = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($condition) {
            return new Condition($condition['ConditionId'], $condition['ConditionName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching item conditions: " . $e->getMessage());
    }
}



// Get Category by Name
static function getItemCategoryByName(PDO $db, string $categoryName) : ?Category {
    try {
        $stmt = $db->prepare('SELECT CategoryId, CategoryName FROM ProductCategory WHERE CategoryName = ?');
        $stmt->execute([$categoryName]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            return new Category($category['CategoryId'], $category['CategoryName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching category: " . $e->getMessage());
    }
}






// Get item size
static function getItemSize(PDO $db, int $id) : ?Size {
    try {
        $stmt = $db->prepare('
            SELECT ItemSize.SizeId, SizeName
            FROM ItemSize 
            JOIN Item ON ItemSize.SizeId = Item.SizeId
            WHERE Item.ItemId = ?
        ');
        $stmt->execute(array($id));
        $size = $stmt->fetch();

        if ($size) {
            return new Size($size['SizeId'], $size['SizeName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching item sizes: " . $e->getMessage());
    }
}

// Get item model
static function getItemModel(PDO $db, int $id) : ?Model {
    try {
        $stmt = $db->prepare('
            SELECT ItemModel.ModelId, ModelName
            FROM ItemModel 
            JOIN Item ON ItemModel.ModelId = Item.ModelId
            WHERE Item.ItemId = ?
            
        ');
        $stmt->execute(array($id));
        $model = $stmt->fetch();

        if ($model) {
            return new Model($model['ModelId'], $model['ModelName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching item models: " . $e->getMessage());
    }
    }

    // Get item image
    static function getItemImage(PDO $db, int $id) : array {
        try {
            $stmt = $db->prepare('
                SELECT ItemImage.ImageId, ImageUrl
                FROM ItemImage 
                JOIN ProductImage ON ItemImage.ImageId = ProductImage.ImageId
                WHERE ItemImage.ItemId = ?
            ');
            $stmt->execute(array($id));
            $images = array();

            while ($image = $stmt->fetch()) {
                $images[] = new Image($image['ImageId'], $image['ImageUrl']);
            }

            return $images;
        } catch (PDOException $e) {
            throw new Exception("Error fetching item images: " . $e->getMessage());
        }
    }

    // Save item 
    static function saveItem(PDO $db, int $sellerId, string $title, string $description, float $price, string $listingDate, array $imageUrls) : string {
        try {
            $stmt = $db->prepare('
                INSERT INTO Item (SellerId, Title, Description, Price, ListingDate)
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute(array($sellerId, $title, $description, $price, $listingDate));
            $itemId = $db->lastInsertId();

            foreach ($imageUrls as $imageUrl) {
                $stmt = $db->prepare('INSERT INTO ProductImage (ImageUrl) VALUES (?)');
                $stmt->execute([$imageUrl]);
                $imageId = $db->lastInsertId();

                $stmt = $db->prepare('INSERT INTO ItemImage (ItemId, ImageId) VALUES (?, ?)');
                $stmt->execute([$itemId, $imageId]);
            }

            return $itemId;
        } catch (PDOException $e) {
            throw new Exception("Error saving item: " . $e->getMessage());
        }
    }
    // Get items by title
static function searchItemsByTitle(PDO $db, string $title, int $count) : array {
    try {
        $stmt = $db->prepare('
            SELECT ItemId, SellerId, Title, Description, Price, ListingDate
            FROM Item
            WHERE Title LIKE ? 
            LIMIT ?
        ');
        $stmt->bindValue(1, '%' . $title . '%', PDO::PARAM_STR);
        $stmt->bindValue(2, $count, PDO::PARAM_INT);
        $stmt->execute();
        $items = array();

        while ($item = $stmt->fetch()) {
            $active = (bool) $item['Active'];
            $items[] = new Item(
                $item['ItemId'],
                $item['SellerId'],
                $item['Title'],
                $item['Description'],
                $item['Price'],
                $item['ListingDate'],
                $active
            );
        }

        return $items;
    } catch (PDOException $e) {
        throw new Exception("Error fetching items by title: " . $e->getMessage());
    }
}

//Get all Brands
static function getBrands(PDO $db) : array {
    try {
        $stmt = $db->query('SELECT BrandName FROM ItemBrand');
        $brands = array();

        while ($brand = $stmt->fetch()) {
            $brands[] = $brand['BrandName'];
        }

        return $brands;
    } catch (PDOException $e) {
        throw new Exception("Error fetching brands: " . $e->getMessage());
    }

}

//Get all Conditions
static function getConditions(PDO $db) : array {
    try {
        $stmt = $db->query('SELECT ConditionName FROM ItemCondition');
        $conditions = array();

        while ($condition = $stmt->fetch()) {
            $conditions[] =  $condition['ConditionName'];
        }

        return $conditions;
    } catch (PDOException $e) {
        throw new Exception("Error fetching conditions: " . $e->getMessage());
    }


}

//Get all Sizes
static function getSizes(PDO $db) : array {
    try {
        $stmt = $db->query('SELECT SizeName FROM ItemSize');
        $sizes = array();

        while ($size = $stmt->fetch()) {
            $sizes[] = $size['SizeName'];
        }

        return $sizes;
    } catch (PDOException $e) {
        throw new Exception("Error fetching sizes: " . $e->getMessage());
    }

}

//Get all Models
static function getModels(PDO $db) : array {
    try {
        $stmt = $db->query('SELECT  ModelName FROM ItemModel');
        $models = array();

        while ($model = $stmt->fetch()) {
            $models[] = ($model['ModelName']);
        }

        return $models;
    } catch (PDOException $e) {
        throw new Exception("Error fetching models: " . $e->getMessage());
    }

}

//Get all Categories
static function getCategories(PDO $db) : array {
    try {
        $stmt = $db->query('SELECT CategoryName FROM ProductCategory');
        $categories = array();

        while ($category = $stmt->fetch()) {
            $categories[] = $category['CategoryName'];
        }

        return $categories;
    } catch (PDOException $e) {
        throw new Exception("Error fetching categories: " . $e->getMessage());
    }

}


//Create a new Brand
static function createBrand(PDO $db, string $brandName) :int{
    try {
        // Verifying if the brand already exists
        if (!self::brandExists($db, $brandName)) {
            $stmt = $db->prepare('INSERT INTO ItemBrand (BrandName) VALUES (?)');
            $stmt->execute([$brandName]);
            return 0;
        }
    } catch (PDOException $e) {
        throw new Exception("Error creating brand: " . $e->getMessage());
    }
    return 1;
}

// Function to check if a brand already exists
static function brandExists(PDO $db, string $brandName) {
    $stmt = $db->prepare('SELECT COUNT(*) FROM ItemBrand WHERE BrandName = ?');
    $stmt->execute([$brandName]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}

//Create a new Condition
static function createCondition(PDO $db, string $conditionName) {
    try {
        if(!self::conditionExists($db, $conditionName)){
            $stmt = $db->prepare('INSERT INTO ItemCondition (ConditionName) VALUES (?)');
            $stmt->execute([$conditionName]);
            return 0;
        }
    } catch (PDOException $e) {
        throw new Exception("Error creating condition: " . $e->getMessage());   
    }
    return 1;
}
static function conditionExists(PDO $db, string $conditionName) {
    $stmt = $db->prepare('SELECT COUNT(*) FROM ItemCondition WHERE ConditionName = ?');
    $stmt->execute([$conditionName]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}

//Create a new Size
static function createSize(PDO $db, string $sizeName) {
    try {
        if(!self::sizeExists($db, $sizeName)){
            $stmt = $db->prepare('INSERT INTO ItemSize (SizeName) VALUES (?)');
            $stmt->execute([$sizeName]);
            return 0;
        }
    } catch (PDOException $e) {
        throw new Exception("Error creating size: " . $e->getMessage());
    }
    return 1;
}
static function sizeExists(PDO $db, string $sizeName) {
    $stmt = $db->prepare('SELECT COUNT(*) FROM ItemSize WHERE SizeName = ?');
    $stmt->execute([$sizeName]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}
//Create a new Model
static function createModel(PDO $db, string $modelName) {
    try {
        if(!self::modelExists($db, $modelName)){
            $stmt = $db->prepare('INSERT INTO ItemModel (ModelName) VALUES (?)');
            $stmt->execute([$modelName]);
            return 0;
        }
    } catch (PDOException $e) {
        throw new Exception("Error creating model: " . $e->getMessage());
    }
    return 1;
}
static function modelExists(PDO $db, string $modelName) {
    $stmt = $db->prepare('SELECT COUNT(*) FROM ItemModel WHERE ModelName = ?');
    $stmt->execute([$modelName]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}

//Create a new Category
static function createCategory(PDO $db, string $categoryName) {
    try {
        if(!self::categoryExists($db, $categoryName)){
            $stmt = $db->prepare('INSERT INTO ProductCategory (CategoryName) VALUES (?)');
            $stmt->execute([$categoryName]);
            return 0;
        }
    } catch (PDOException $e) {
        throw new Exception("Error creating category: " . $e->getMessage());
    }
    return 1;
}
static function categoryExists(PDO $db, string $categoryName) {
    $stmt = $db->prepare('SELECT COUNT(*) FROM ProductCategory WHERE CategoryName = ?');
    $stmt->execute([$categoryName]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}



static function getConditionsObj(PDO $db): array {
    try {
        $stmt = $db->query('SELECT ConditionId, ConditionName FROM ItemCondition');
        $conditions = array();

        while ($condition = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $conditions[] = new Condition($condition['ConditionId'], $condition['ConditionName']);
        }

        return $conditions;
    } catch (PDOException $e) {
        throw new Exception("Error fetching conditions: " . $e->getMessage());
    }
}


static function getSizesObj(PDO $db): array {
    try {
        $stmt = $db->query('SELECT SizeId, SizeName FROM ItemSize');
        $sizes = array();

        while ($size = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sizes[] = new Size($size['SizeId'], $size['SizeName']);
        }

        return $sizes;
    } catch (PDOException $e) {
        throw new Exception("Error fetching sizes: " . $e->getMessage());
    }
}

// Get Brand by Name

// Get Model by Name
static function getItemModelByName(PDO $db, string $modelName) : ?Model {
    try {
        $stmt = $db->prepare('SELECT ModelId, ModelName FROM ItemModel WHERE ModelName = ?');
        $stmt->execute([$modelName]);
        $model = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($model) {
            return new Model($model['ModelId'], $model['ModelName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching model: " . $e->getMessage());
    }
}

// Get Condition by Name


// Get Size by Name
static function getItemSizeByName(PDO $db, string $sizeName) : ?Size {
    try {
        $stmt = $db->prepare('SELECT SizeId, SizeName FROM ItemSize WHERE SizeName = ?');
        $stmt->execute([$sizeName]);
        $size = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($size) {
            return new Size($size['SizeId'], $size['SizeName']);
        } else {
            return null;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching size: " . $e->getMessage());
    }
}

//Remove a Category
static function removeCategory(PDO $db, string $categoryName):int {
    try {
        $stmt = $db->prepare('DELETE FROM ProductCategory WHERE CategoryName = ?');
        $stmt->execute([$categoryName]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error removing category: " . $e->getMessage());
    }
    return 1;
}

//Remove a Brand
static function removeBrand(PDO $db, string $brandName) :int{
    try {
        $stmt = $db->prepare('DELETE FROM ItemBrand WHERE BrandName = ?');
        $stmt->execute([$brandName]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error removing brand: " . $e->getMessage());
    }
    return 1;
}

//Remove a Condition
static function removeCondition(PDO $db, string $conditionName) :int{
    try {
        $stmt = $db->prepare('DELETE FROM ItemCondition WHERE ConditionName = ?');
        $stmt->execute([$conditionName]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error removing condition: " . $e->getMessage());
    }
    return 1;
}

//Remove a Size
static function removeSize(PDO $db, string $sizeName) :int {
    try {
        $stmt = $db->prepare('DELETE FROM ItemSize WHERE SizeName = ?');
        $stmt->execute([$sizeName]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error removing size: " . $e->getMessage());
    }
    return 1;
}

//Remove a Model
static function removeModel(PDO $db, string $modelName) :int {
    try {
        $stmt = $db->prepare('DELETE FROM ItemModel WHERE ModelName = ?');
        $stmt->execute([$modelName]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error removing model: " . $e->getMessage());
    }
    return 1;
}
//get all active items
static function getAllActiveItems(PDO $db) : array {
    try {
        $stmt = $db->query('
            SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
            FROM Item
            WHERE Active = 1
        ');
        $items = array();

        while ($item = $stmt->fetch()) {
            $active = (bool) $item['Active'];
            $items[] = new Item(
                $item['ItemId'],
                $item['SellerId'],
                $item['Title'],
                $item['Description'],
                $item['Price'],
                $item['ListingDate'],
                $active
            );
        }

        return $items;
    } catch (PDOException $e) {
        throw new Exception("Error fetching active items: " . $e->getMessage());
    }
}


//get all items
static function getAllItems(PDO $db) : array {
    try {
        $stmt = $db->query('
            SELECT ItemId, SellerId, Title, Description, Price, ListingDate, Active
            FROM Item
        ');
        $items = array();

        while ($item = $stmt->fetch()) {
            $active = (bool) $item['Active'];
            $items[] = new Item(
                $item['ItemId'],
                $item['SellerId'],
                $item['Title'],
                $item['Description'],
                $item['Price'],
                $item['ListingDate'],
                $active
            );
        }

        return $items;
    } catch (PDOException $e) {
        throw new Exception("Error fetching items: " . $e->getMessage());
    }
}

//GET ALL IMAGES URL from all items
static function getAllImages(PDO $db) : array {
    try {
        $stmt = $db->query('SELECT ImageUrl FROM ProductImage');
        $images = array();

        while ($image = $stmt->fetch()) {
            $images[] = $image['ImageUrl'];
        }

        return $images;
    } catch (PDOException $e) {
        throw new Exception("Error fetching images: " . $e->getMessage());
    }
}

//Delete an item
static function deleteItem(PDO $db, int $itemId) :int {
    try {
        $stmt = $db->prepare('DELETE FROM Item WHERE ItemId = ?');
        $stmt->execute([$itemId]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error deleting item: " . $e->getMessage());
    }
    return 1;
}

//delete all images from an item
static function deleteItemImages(PDO $db, int $itemId) :int {
    try {
        $stmt = $db->prepare('DELETE FROM ItemImage WHERE ItemId = ?');
        $stmt->execute([$itemId]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error deleting item images: " . $e->getMessage());
    }
    return 1;

}
//delete all categories from an item
static function deleteItemCategories(PDO $db, int $itemId) :int {
    try {
        $stmt = $db->prepare('DELETE FROM ItemCategory WHERE ItemId = ?');
        $stmt->execute([$itemId]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error deleting item categories: " . $e->getMessage());
    }
    return 1;

}


//delete all item ratings
static function deleteItemRatings(PDO $db, int $itemId) :int {
    try {
        $stmt = $db->prepare('DELETE FROM Rating WHERE ItemId = ?');
        $stmt->execute([$itemId]);
        return 0;
    } catch (PDOException $e) {
        throw new Exception("Error deleting item ratings: " . $e->getMessage());
    }
    return 1;
}

}
?>