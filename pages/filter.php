<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/search.tpl.php');



$db = getDatabaseConnection();

if(isset($_GET['category'])) {
    $categoryName = $_GET['category'];
} else {
    $categoryName = null;
}
if(isset($_GET['brand'])) {
    $brandName = $_GET['brand'];
} else {
    $brandName = null;
}
if(isset($_GET['condition'])) {
    $condition = $_GET['condition'];
} else {
    $condition = null;
}

if(isset($_GET['size'])) {
    $size = $_GET['size'];
} else {
    $size = null;
}
if(isset($_GET['model'])) {
    $model = $_GET['model'];
} else {
    $model = null;
}
if(isset($_GET['minPrice'])) {
    $minPrice = intval($_GET['minPrice']);
} else {
    $minPrice = null;
}
if(isset($_GET['maxPrice'])) {
    $maxPrice = intval($_GET['maxPrice']);
} else {
    $maxPrice = null;
}





drawHeader($session, $db);
drawFilteredProducts($session,$db,$categoryName,$brandName,$condition,$size,$model,$minPrice,$maxPrice);
drawFooter();
?>
