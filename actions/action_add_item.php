<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    die(header('Location: /'));
}

$db = getDatabaseConnection();
$user = User::getUser($db, $session->getId());

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $session->addMessage('error', 'Invalid CSRF token');
        header('Location: ../pages');
        exit();
    }
    $productName = trim($_POST['productname'] ?? '');
    $price = floatval($_POST['price'] ?? 0); 
    $description = trim($_POST['description'] ?? '');
    $brandname = trim($_POST['brand'] ?? '');
    $modelname = trim($_POST['model'] ?? '');
    $conditionname = trim($_POST['condition'] ?? '');
    $sizename = trim($_POST['size'] ?? '');
    $category1name = trim($_POST['category1'] ?? '');
    $category2name = trim($_POST['category2'] ?? '');
    $category3name = trim($_POST['category3'] ?? '');

    $productNamePattern = '/^.{5,50}$/';
    $descriptionPattern = '/^.{5,200}$/';

    if (empty($productName) || $price <= 0 || empty($description) || 
        !preg_match($productNamePattern, $productName) || !preg_match($descriptionPattern, $description)) {
        $session->addMessage('error', 'Please fill in all required fields with valid data.');
        redirectToPostcreation();
        exit();
    }
    // Fetch related data
    $brand = $brandname ? Item::getItemBrandByName($db, $brandname) : null;
    $model = $modelname ? Item::getItemModelByName($db, $modelname) : null;
    $condition = $conditionname ? Item::getItemConditionByName($db, $conditionname) : null;
    $size = $sizename ? Item::getItemSizeByName($db, $sizename) : null;

    // Validate condition and size
    if (!$condition || !$size) {
        $session->addMessage('error', 'Please fill in all required fields.');
        redirectToPostcreation();
        exit();
    }

    // Fetch categories
    $categories = [];
    if ($category1name && $category1name != "none") {
        $category1 = Item::getItemCategoryByName($db, $category1name);
        if ($category1) $categories[] = $category1;
    }
    if ($category2name && $category2name != "none") {
        $category2 = Item::getItemCategoryByName($db, $category2name);
        if ($category2) $categories[] = $category2;
    }
    if ($category3name && $category3name != "none") {
        $category3 = Item::getItemCategoryByName($db, $category3name);
        if ($category3) $categories[] = $category3;
    }

    // Insert item into database
    $sql = "INSERT INTO Item (SellerId, Title, Description, Price, BrandId, ModelId, ConditionId, SizeId)
            VALUES (:sellerId, :productName, :description, :price, :brandId, :modelId, :conditionId, :sizeId)";
    $stmt = $db->prepare($sql);

    if ($stmt->execute([
        'sellerId' => $user->userId, 
        'productName' => $productName, 
        'description' => $description, 
        'price' => $price, 
        'brandId' => $brand ? $brand->brandId : null, 
        'modelId' => $model ? $model->modelId : null, 
        'conditionId' => $condition->conditionId, 
        'sizeId' => $size->sizeId
    ])) {
        $itemId = $db->lastInsertId();

        // Handle file uploads
        $uploadDirectory = '../database/uploads/';
        $imageUrls = [];
        $uploadError = false;

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if (!empty($_FILES['images']['name'][$key])) {
                $file_name = basename($_FILES['images']['name'][$key]);
                $file_tmp = $_FILES['images']['tmp_name'][$key];
                $file_type = $_FILES['images']['type'][$key];

                // Validate file type
                if (in_array($file_type, ['image/jpeg', 'image/png'])) {
                    $itemFolder = $uploadDirectory . 'item_' . $itemId . '/';

                    if (!file_exists($itemFolder)) {
                        mkdir($itemFolder, 0777, true);
                    }

                    $targetFilePath = $itemFolder . $file_name;

                    if (move_uploaded_file($file_tmp, $targetFilePath)) {
                        $imageUrls[] = $targetFilePath;
                    } else {
                        $session->addMessage('error', 'Failed to upload image: ' . $file_name);
                        $uploadError = true;
                        break;
                    }
                } else {
                    $session->addMessage('error', 'Only JPEG and PNG files are allowed: ' . $file_name);
                    $uploadError = true;
                    break;
                }
            }
        }

        if (!$uploadError) {
            if (empty($imageUrls)) {
                $defaultImageUrl = "database/uploads/default_item.png";
                $imageUrls[] = $defaultImageUrl;
            }

            // Insert images into database
            foreach ($imageUrls as $imageUrl) {
                $sql = "INSERT INTO ProductImage (ImageUrl) VALUES (:imageUrl)";
                $stmt = $db->prepare($sql);
                if ($stmt->execute(['imageUrl' => $imageUrl])) {
                    $imageId = $db->lastInsertId();

                    $sql = "INSERT INTO ItemImage (ItemId, ImageId) VALUES (:itemId, :imageId)";
                    $stmt = $db->prepare($sql);
                    if (!$stmt->execute(['itemId' => $itemId, 'imageId' => $imageId])) {
                        $session->addMessage('error', 'Failed to associate image with item');
                        redirectToPostcreation();
                        exit();
                    }
                } else {
                    $session->addMessage('error', 'Failed to save image URL in the database');
                    redirectToPostcreation();
                    exit();
                }
            }

            // Associate item with categories
            foreach ($categories as $category) {
                $sql = "INSERT INTO ItemCategory (ItemId, CategoryId) VALUES (:itemId, :categoryId)";
                $stmt = $db->prepare($sql);
                if (!$stmt->execute(['itemId' => $itemId, 'categoryId' => $category->categoryId])) {
                    $session->addMessage('error', 'Failed to associate item with category');
                    redirectToPostcreation();
                    exit();
                }
            }

            $session->addMessage('success', 'Item successfully published');
            header("Location: ../pages/post.php?id=$itemId");
            exit();
        } else {
            redirectToPostcreation();
            exit();
        }
    } else {
        $session->addMessage('error', 'Failed to insert item into the database');
        redirectToPostcreation();
        exit();
    }
}

function redirectToPostcreation() {
    $_SESSION['redirect_to_postc'] = true;
    header('Location: ../pages/postcreation.php');
    exit();
}
?>
