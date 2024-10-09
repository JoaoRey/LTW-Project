<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/wishlist.class.php');
require_once(__DIR__ . '/../database/item.class.php'); 

function drawWishlist(Session $session, $db)
{
    try {
        if (!$session->isLoggedIn()) {
            header('Location: /pages/login.php');
            exit();
        }

        $userId = $session->getId();

        $wishlistItems = Wishlist::getItemsByUser($db, $userId);
        
        
      
        if (!empty($wishlistItems)) {
            ?>
            <main>
                <section id="wishlist">
                    <div class="cart-items">
                        <?php foreach ($wishlistItems as $wishlistItem) : ?>
                            
                            <?php
                            $image = Item::getItemImage($db, $wishlistItem->itemId)[0]; 
                            $item= Item::getItem($db, $wishlistItem->itemId);
                            
                            
                            $brand = Item::getItemBrand($db, $item->itemId);
                            $brandName = $brand->brandName;

                            $condition = Item::getItemCondition($db, $item->itemId);
                            $conditionName = $condition->conditionName;
                            ?>
                            <div class="cart-item">
                                <div class="img-name-condition">
                                    <img class="img-product-cart" src="../<?= $image->imageUrl ?>" alt="<?= $item->title ?>" style="width: 12em; height: 10em;">
                                    <div class="name-condition">
                                        <h1><?= htmlspecialchars($item->title) ?></h1>
                                        <p class="condition-cart"><strong>Condition:</strong> <?= htmlspecialchars($conditionName) ?></p>
                                    </div>
                                </div>
                                <p class="brand-cart"><strong>Brand:</strong> <?= htmlspecialchars($brandName) ?> </p>
                                <p><?= $item->price ?>€</p>
                                <form class="cart-form" action="../actions/add_to_cart_and_remove_from_wishlist.php" method="post">
                                <input type="hidden" name="item_id" value="<?= $item->itemId ?>">
                                 <input type="hidden" name="wishlist_id" value="<?= $wishlistItem->wishlistId ?>">
                                    <button type="submit">Add to cart</button>
                                </form>
                                <form class="cart-form" action="../actions/remove_from_wishlist.php" method="post">
                                    <input type="hidden" name="wishlist_id" value="<?= $wishlistItem->wishlistId ?>">
                                    <button type="submit">Remove</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                
                </section>
            </main>
            <?php
        } else {
            ?>
            <main>
                <section id="wishlist">
                    <div class="cart-items">
                        <h3 id="no-items">No items currently in the wishlist</h3>
                    </div>
            
                        <!-- You can add a button here for further actions -->
                    
                </section>
            </main>
            <?php
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>