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
$searchQuery = $_GET['query'];
$items = Item::getItemsbyName($db, $searchQuery, 8); 


drawHeader($session, $db);
drawSearchedProducts($session,$db,$items,$searchQuery);
drawFooter();

?>
