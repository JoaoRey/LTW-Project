<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/cart.class.php');
require_once(__DIR__ . '/../database/item.class.php'); 
require_once(__DIR__ . '/../database/user.class.php');



function drawCheckout(Session $session, $db, $item, $userId) {
    $itemId = $item->itemId;
    try {
        // Obtém as informações do usuário do banco de dados
        $user = User::getUser($db, $userId);        

        ?>
        <main>
            <section class="publish-section">
                <h2>Checkout</h2>
                
                <form action="/../actions/action_checkout.php" method="post">
                <div class="publish-div">
                    <h3>Informações de Envio</h3>
                        <label for="address">Endereço:</label>
                        <input type="text" id="address" name="address" value="<?= isset($user->address) ? htmlspecialchars($user->address) : '' ?>" required><br>
                        <label for="postal_code">Código Postal:</label>
                        <input type="text" id="postal_code" name="postal_code" value="<?= isset($user->postalCode) ? htmlspecialchars($user->postalCode) : '' ?>" required><br>
                        <label for="city">Cidade:</label>
                        <input type="text" id="city" name="city" value="<?= isset($user->city) ? htmlspecialchars($user->city) : '' ?>" required><br>
                        <label for="district">Distrito:</label>
                        <input type="text" id="district" name="district" value="<?= isset($user->district) ? htmlspecialchars($user->district) : '' ?>" required><br>
                        <label for="country">País:</label>
                        <input type="text" id="country" name="country" value="<?= isset($user->country) ? htmlspecialchars($user->country) : '' ?>" required><br>
                    
                    <h3>Payment options</h3>
                    <label for="payment_method">Payment method:</label>
                    <select id="payment_method" class="publish-select" name="payment_method" required>
                        <option value="" selected disabled>Select...</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                    </select><br>
                    
                    <div id="credit_card_fields" style="display: none;">
                        <label for="card_number">Card Number:</label>
                        <input type="text" id="card_number" name="card_number"required ><br>
                        <label for="card_holder">Card Name:</label>
                        <input type="text" id="card_holder" name="card_holder"required><br>
                        <label for="expiration_date">Expire Date:</label>
                        <input type="text" id="expiration_date" name="expiration_date"required><br>
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" name="cvv"required><br>
                    </div>
                    
                    <div id="paypal_fields" style="display: none;">
                        <label for="paypal_email">Email address of PayPal:</label>
                        <input type="email" id="paypal_email" name="paypal_email"required><br>
                    </div>
                    <input type="hidden" name="checkout_submitted" value="1">
                    <input type="hidden" name="item_ids[]" value="<?= $itemId ?>">

                    <button type="submit" id="finalizar_compra_btn" onclick="simulatePurchaseAndRedirect()">Finish purchase</button>
                    </div>
                </form>
            </section>
        </main>

    <?php
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
