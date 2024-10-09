<?php
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/communication.class.php');

function drawConversations(Session $session, $db) {
    $userId = $session->getId();
    

    $conversations = Communication::getAllChats($db);
    echo "<main id='conversation-main'>";
    echo "<h1>" . htmlentities("Conversas Ativas") . "</h1>";
    echo "<ul>";
    if(empty($conversations)) {
        echo "<li>No open conversations</li>";
    }
    foreach (array_reverse($conversations) as $conversation) {
        $otherUserId = ($userId == $conversation['senderId']) ? $conversation['receiverId'] : $conversation['senderId'];
        $item = $conversation['itemId'];
        $otherName = htmlentities(User::getUsernameById($db, $otherUserId));
        $itemName = htmlentities(Item::getItemNameById($db, $item));
        $image = Item::getItemImage($db, $item);
        ?>
        <li class="conversation-li">
            <img class="conversation-img" src="../<?= $image[0]->imageUrl ?>" height="200" width="200">
            
            <h3><?= htmlentities("Conversa com {$otherName} sobre {$itemName}") ?></h3>
            
            <form action="/pages/chat.php" method="post" >
                <input type="hidden" name="owner_id" value="<?= $otherUserId ?>">
                <input type="hidden" name="item_id" value="<?= $item ?>">
                <button type="submit">Chat</button>
            </form>
        </li>
        <?php
    }
    echo "</ul>";
    echo "</main>";
}
?>