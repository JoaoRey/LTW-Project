<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/cart.class.php');
require_once(__DIR__ . '/../database/item.class.php'); 

function drawCart(Session $session, $db)
{
    try {
        if (!$session->isLoggedIn()) {
            header('Location: /pages/login.php');
            exit();
        }

        $userId = $session->getId();

        $cartItems = Cart::getItemsByUser($db, $userId);
        
        $totalPrice = 0;
      
        if (!empty($cartItems)) {
            ?>
            <main>
                <section id="cart">
                    <div class="cart-items">
                        <?php foreach ($cartItems as $cartItem) : ?>
                            
                            <?php
                            $image = Item::getItemImage($db, $cartItem->itemId)[0]; 
                            $item= Item::getItem($db, $cartItem->itemId);
                            $totalPrice += $item->price;
                            
                            $brand = Item::getItemBrand($db, $item->itemId);
                            $brandName = $brand->brandName;

                            $condition = Item::getItemCondition($db, $item->itemId);
                            $condition->conditionName;
                           
                            
                            ?>
                            <div class="cart-item">
                            <div class="img-name-condition">
                              <img class="img-product-cart" src="../<?= $image->imageUrl ?>" alt="<?= $item->title ?>" style="width: 12em; height: 10em;">
                                <div class="name-condition">
                                    <h1><?= htmlspecialchars($item->title) ?></h1>
                                    <p class="condition-cart"><strong>Condition:</strong> <?= htmlspecialchars($condition->conditionName) ?></p>
                                </div>
                            </div>
                            <p class="brand-cart"><strong>Brand:</strong> <?= !empty($brandName) ? htmlspecialchars($brandName) : " - "?> </p>
                            <p><?= $item->price ?>€</p>
                                <form class="cart-form" action="../actions/remove_from_cart.php" method="post">
                                  <input type="hidden" name="cart_id" value="<?= $cartItem->cartId ?>">
                                  <button type="submit">Trash</button>
                                </form>

                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-total">
                        <h1>Total Price:</h1>
                        <p class="total-price"><?= number_format($totalPrice, 2) ?>€</p>
                        <form id="cart-form" action="../pages/checkout.php" method="post">
                        <?php foreach ($cartItems as $cartItem) : ?>
                            <input type="hidden" name="item_ids[]" value="<?= $cartItem->itemId ?>">
                        <?php endforeach; ?>
                        <button id="pay-cart-button" type="submit">Pay</button>
                    </form>
            
                    </div>
                </section>
            </main>
        <?php
        } else {
            ?>
             <main>
                <section id="cart">
                <div class="cart-items">
                        <h3 id="no-items">No items currently in the cart</h3>
                    </div>
                    
                    <div class="cart-total">
                        <h1>Total Price:</h1>
                        <p class="total-price"><?= number_format($totalPrice, 2) ?>€</p>
                        <form id="cart-form" action="../pages" method="post">
                            <button id="pay-cart-button" type="submit">Pay</button>
                        </form>
                    </div>
                </section>
             </main>
            <?php
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
