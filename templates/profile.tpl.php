<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/payment.class.php');
require_once(__DIR__ . '/../actions/action_editprofile.php');
require_once(__DIR__ . '/../actions/action_change_password.php');
require_once(__DIR__ . '/../database/review.class.php');

function drawProfile(Session $session, $db)
{
    
    try {
        if (!$session->isLoggedIn()) {
            header('Location: /pages/login.php');
            exit();
        }

        $userId = $session->getId();

        $user = User::getUser($db, $userId);

        $presentedProducts = User::fetchPresentedProducts($db, $userId);
        
    
        ?>
        <main id="profilepage">
            <div id="profile-img-infos">
                <img id="profile-img" src="<?= !empty($user->imageUrl) ? htmlspecialchars($user->imageUrl) : "../Docs/img/9024845_user_circle_light_icon.png" ?>" alt="" height="100">
                <div id="profilepage-name-loc">
                    <h1><?= htmlspecialchars($user->name()) ?></h1>
                    <h2><?= !empty($user->city) ? htmlspecialchars($user->city) : " - " ?>, <?= !empty($user->district) ? htmlspecialchars($user->district) : " - " ?>, <?= !empty($user->country) ? htmlspecialchars($user->country) : " - "?></h2>
                </div>
            </div>
            <div id="left-profile-page">
                <button class="button-left-prof" onclick="toggleProfileProd()">
                    Published products
                </button>
                <button onclick="togglePurchaseHistory()" class="button-left-prof">Purchase History</button> 
                <button onclick="toggleReviewsSection()" class="button-left-prof">Reviews</button>


                <button onclick="toggleEditProfile()" class="button-left-prof">
                    Edit Profile
                </button>
                <button onclick="toggleChangePass()" class="button-left-prof">
                    Change Password
                </button>

                <?php if($session->isAdmin()) { ?>
                <button onclick="toggleAdminSection()" class="button-left-prof">
                    Admin options
                </button>
                <?php } ?>
            </div>
            
            <div>
                <div id="profile-presented">
                        <h1>Presented Products</h1>
                        <h2>To Sell:</h2>
                        <div class="profile-page-items">
                        <?php 
                        $allItems=Item::getItemsBySellerId($db, $userId);
                        $activeItems = [];
                        $inactiveItems = [];
        
                        foreach ($allItems as $item) {
                            if ($item->active === true) {
                                $activeItems[] = $item;
                            } else {
                                $inactiveItems[] = $item;
                            }
                        }
                        if(empty($activeItems)){
                            echo "<h3>No items to sell</h3>";
                        }
                        foreach ($activeItems as $product) : ?>
                                <article class="">
                                    <?php
                                    $image = Item::getItemImage($db, $product->itemId)[0];
                                    ?>
                                    <a href="/pages/post.php?id=<?= $product->itemId ?>" class="profilepage-item">
                                        <img class="profilepage-img-item" src="../<?=$image->imageUrl?>" alt="" width="100">
                                        <div class="profilepage-title-price-item">
                                            <h1><?= htmlspecialchars($product->title) ?></h1>
                                            <h2><?= $product->price ?>€</h2>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                                
                        </div>
                        <h2>Sold:</h2>
                        <div class="profile-page-items">
                        <?php
                        if(empty($inactiveItems)){
                            echo "<h3>No items sold</h3>";
                        }
                        foreach ($inactiveItems as $product) : ?>
                                <article class="">
                                    <?php
                                    $image = Item::getItemImage($db, $product->itemId)[0];
                                    ?>
                                    <a href="/pages/post.php?id=<?= $product->itemId ?>" class="profilepage-item">
                                        <img class="profilepage-img-item" src="../<?=$image->imageUrl?>" alt="" width="100">
                                        <div class="profilepage-title-price-item">
                                            <h1><?= htmlspecialchars($product->title) ?></h1>
                                            <h2><?= $product->price ?>€</h2>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                    </div>
                </div>




                <div id="purchase-history" style="display: none;">
                    <h1>Purchase History</h1>
                    
                        <div class="profile-page-items">
                        <?php 
                        $payments= Payment::getPaymentsByBuyerId($db, $userId);
                        foreach($payments as $payment):
                            $item = Item::getItem($db, $payment->itemId);
                            $image = Item::getItemImage($db, $item->itemId)[0];
                            ?>
                            <article class="">
                                <a href="/pages/postbought.php?id=<?= $item->itemId ?>" class="profilepage-item">
                                    <img class="profilepage-img-item" src="../<?=$image->imageUrl?>" alt="" width="100">
                                    <div class="profilepage-title-price-item">
                                        <h1><?= htmlspecialchars($item->title) ?></h1>
                                        <h2><?= $item->price ?>€</h2>
                                    </div>
                                </a>
                            </article>
                            <?php endforeach; 
                            if(empty($payments)){
                                ?><h3>No purchase history</h3>
                            <?php } ?>
                        </div>  
                </div>


                
                <div id="reviews-section" style="display: none;">
                    <div id="reviews-header">
                        <?php 
                        try {
                            $averageRating = Review::calculateAverageRating($db, $userId);
                        } catch (Exception $e) {
                            $averageRating = 0.0;
                            echo "Error: " . $e->getMessage();
                        }
                        ?>
                <h2>Reviews <span id="average-rating"><?= number_format($averageRating, 2) ?></span><span id="average-stars" class="stars"></span></h2>
                    </div>
                    <div class="reviews-container">
                        <div class="reviews-column">
                            <h3>Reviews Made:</h3>
                            <div id="reviews-made">
                                <?php 
                                try {
                                    $reviewsMade = Review::getReviewsMadebyId($db, $userId);
                                } catch (Exception $e) {
                                    echo "Error: " . $e->getMessage();
                                    $reviewsMade = [];
                                }

                                if (empty($reviewsMade)): ?>
                                    <p>No reviews made</p>
                                <?php else: ?>
                                    <?php foreach ($reviewsMade as $review): 
                                        $item = Item::getItem($db, $review->itemId);
                                        $image = Item::getItemImage($db, $item->itemId);
                                        ?>
                                        <div class="review-item">
                                            <img src="../<?= htmlspecialchars($image[0]->imageUrl) ?>" alt="<?= htmlspecialchars($item->title) ?>" class="item-image" style="width: 40%; height: 40%;">
                                            <div class="review-details">
                                                <p><strong>Item:</strong> <?= htmlspecialchars($item->title) ?></p>
                                                <p><strong>Rating:</strong> <span class="stars" data-rating="<?= number_format($review->rating,2) ?>"></span></p>
                                                <p><strong>Comment:</strong> <?= htmlspecialchars($review->comment) ?></p>
                                                <p><strong>Date:</strong> <?= htmlspecialchars($review->reviewDate) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="reviews-column">
                            <h3>Reviews Received:</h3>
                            <div id="reviews-received">
                                <?php 
                                try {
                                    $reviewsReceived = Review::getReviewsReceivedbyId($db, $userId);
                                } catch (Exception $e) {
                                    echo "Error: " . $e->getMessage();
                                    $reviewsReceived = [];
                                }

                                if (empty($reviewsReceived)): ?>
                                    <p>No reviews received</p>
                                <?php else: ?>
                                    <?php foreach ($reviewsReceived as $review): 
                                        $item = Item::getItem($db, $review->itemId);
                                        $image = Item::getItemImage($db, $item->itemId);
                                        ?>
                                        <div class="review-item">
                                            <img src="../<?= htmlspecialchars($image[0]->imageUrl) ?>" alt="<?= htmlspecialchars($item->title) ?>" class="item-image" style="width: 40%; height: 40%;">
                                            <div class="review-details">
                                                <p><strong>Item:</strong> <?= htmlspecialchars($item->title) ?></p>
                                                <p><strong>Rating:</strong> <span class="stars" data-rating="<?= number_format($review->rating,2) ?>"></span></p>
                                                <p><strong>Comment:</strong> <?= htmlspecialchars($review->comment) ?></p>
                                                <p><strong>Date:</strong> <?= htmlspecialchars($review->reviewDate) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="edit-profile-section" style="display: none;">
                    <form class="profile-edit" action="/actions/action_editprofile.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">

                        <h1>User</h1>
                        <div class="input-group">
                            <label class="image-input">
                                <input name="images[]" type="file" accept="image/heic, image/png, image/jpeg, image/webp" multiple onchange="previewImages(event,1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="3em" height="3em" viewBox="0 0 32 32"><path fill="currentColor" d="M29 26H3a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h6.46l1.71-2.55A1 1 0 0 1 12 4h8a1 1 0 0 1 .83.45L22.54 7H29a1 1 0 0 1 1 1v17a1 1 0 0 1-1 1M4 24h24V9h-6a1 1 0 0 1-.83-.45L19.46 6h-6.92l-1.71 2.55A1 1 0 0 1 10 9H4Z"/><path fill="currentColor" d="M16 22a6 6 0 1 1 6-6a6 6 0 0 1-6 6m0-10a4 4 0 1 0 4 4a4 4 0 0 0-4-4"/></svg>
                                <img class="preview-image" id="preview-image-1" src="" alt="">
                            </label>
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="<?= $user->email ?>" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Enter a valid email address.">
                        </div>
                        <div class="input-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="<?= $user->username ?>" pattern="^[a-zA-Z0-9_]{3,20}$" title="Username should be 3-20 characters long and can include letters, numbers, and underscores.">
                        </div>
                        <div class="name-group">
                            <div class="input-group">
                                <label for="first-name">First Name</label>
                                <input type="text" id="first-name" name="firstName" placeholder="<?= $user->firstName ?>" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="First name should only contain letters and be 1-50 characters long.">
                            </div>
                            <div class="input-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" id="last-name" name="lastName" placeholder="<?= $user->lastName ?>" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="Last name should only contain letters and be 1-50 characters long.">
                            </div>
                        </div>
                        <div class="location-group">
                            <h1>Location</h1>
                            <div class="input-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" placeholder="<?= $user->address ?>" pattern="^[a-zA-Z0-9À-ÖØ-öø-ÿ\s,º.'-]{1,100}$" title="Address can contain letters, numbers, and symbols like comma, dot, apostrophe, and hyphen.">
                            </div>
                            <div class="input-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" placeholder="<?= $user->city ?>" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="City should only contain letters and be 1-50 characters long.">
                            </div>
                            <div class="input-group">
                                <label for="district">District</label>
                                <input type="text" id="district" name="district" placeholder="<?= $user->district ?>" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="District should only contain letters and be 1-50 characters long.">
                            </div>
                            <div class="input-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country" placeholder="<?= $user->country ?>" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="Country should only contain letters and be 1-50 characters long.">
                            </div>
                            <div class="input-group">
                                <label for="postal-code">Postal Code</label>
                                <input type="text" id="postal-code" name="postalCode" placeholder="<?= $user->postalCode ?>" pattern="^\d{4}-\d{3}$" title="Postal code should be in the format dddd-ddd.">
                            </div>
                            <div class="input-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="<?= $user->phone ?>" pattern="^\+?\d{9,12}$" title="Phone number should be 9 digits long, or up to 12 digits if including an optional leading +.">
                            </div>
                        </div>
                        <div class="flex-login-regis">
                            <button type="submit">Change</button>
                        </div>
                    </form>
                </div>



                <div id="change-password" style="display: none;">
                    <form class="profile-edit" action="../actions/action_change_password.php" method="post">
                        <h1>Change Password</h1>
                        <div class="input-group">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" name="currentPassword" required pattern=".{6,}" title="Current password should be at least 6 characters long.">
                        </div>
                        <div class="input-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" name="newPassword" required pattern=".{6,}" title="New password should be at least 6 characters long.">
                        </div>
                        <div class="input-group">
                            <label for="confirm-password">Confirm New Password</label>
                            <input type="password" id="confirm-password" name="confirmPassword" required pattern=".{6,}" title="Confirmation password should be at least 6 characters long.">
                        </div>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                        <div class="flex-login-regis">
                            <button type="submit">Change Password</button>
                        </div>
                    </form>
                </div>



                <div id="admin-section" style="display: none;">
                    <?php if ($session->isAdmin()): ?>
                    
                        <h2>Admin Section</h2>
                        <form action="/../actions/action_create_fields.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                            <label for="brandName">New Brand:</label>
                            <input type="text" id="brandName" name="brandName" required>
                            <button type="submit" name="createBrand">Create Brand</button>
                        </form>
                        <form action="/../actions/action_create_fields.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                            <label for="conditionName">New Condition:</label>
                            <input type="text" id="conditionName" name="conditionName" required>
                            <button type="submit" name="createCondition">Create Condition</button>
                        </form>

                        <form action="/../actions/action_create_fields.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                            <label for="sizeName">New Size:</label>
                            <input type="text" id="sizeName" name="sizeName" required>
                            <button type="submit" name="createSize">Create Size</button>
                        </form>

                        <form action="/../actions/action_create_fields.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                            <label for="modelName">New Model:</label>
                            <input type="text" id="modelName" name="modelName" required>
                            <button type="submit" name="createModel">Create Model</button>
                        </form>

                        <form action="/../actions/action_create_fields.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                            <label for="categoryName">New Category:</label>
                            <input type="text" id="categoryName" name="categoryName" required>
                            <button type="submit" name="createCategory">Create Category</button>
                        </form>
                        <form action="/../actions/action_create_fields.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                            <label for="userId">Select User:</label>
                            <select id="userId" name="userId" class="publish-select" required>
                                <?php
                                $users = User::getAllUsers($db);
                                foreach ($users as $user) {
                                    if(!$user->admin){
                                        echo "<option value=\"{$user->userId}\">{$user->firstName} {$user->lastName}</option>";
                                    }
                                    
                                }
                                ?>
                            </select>
                            <button type="submit" name="elevateAdmin">Elevate User to Admin</button>
                    </form>
                    <form action="/templates/route.php?action=remove-category" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                        <label for="category-select">Remove Category:</label>
                        <select id="category-select" name="category" class="publish-select">
                            <?php
                            $categories = Item::getCategories($db);
                            foreach ($categories as $category) {
                                echo "<option value=\"{$category}\">{$category}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="removeCategory">Remove Category</button>
                    </form>
                    <form action="/templates/route.php?action=remove-model" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                        <label for="model-select">Remove Model:</label>
                        <select id="model-select" name="model" class="publish-select">
                            <?php
                            $models = Item::getModels($db);
                            foreach ($models as $model) {
                                echo "<option value=\"{$model}\">{$model}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="removeModel">Remove Model</button>
                    </form>
                    <form action="/templates/route.php?action=remove-condition" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                        <label for="condition-select">Remove Condition:</label>
                        <select id="condition-select" name="condition" class="publish-select">
                            <?php
                            $conditions = Item::getConditions($db);
                            foreach ($conditions as $condition) {
                                echo "<option value=\"{$condition}\">{$condition}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="removeCondition">Remove Condition</button>
                    </form>
                    <form action="/templates/route.php?action=remove-size" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                        <label for="size-select">Remove Size:</label>
                        <select id="size-select" name="size" class="publish-select">
                            <?php
                            $sizes = Item::getSizes($db);
                            foreach ($sizes as $size) {
                                echo "<option value=\"{$size}\">{$size}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="removeSize">Remove Size</button>
                    </form>
                    <form action="/templates/route.php?action=remove-brand" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">
                        <label for="brand-select">Remove Brand:</label>
                        <select id="brand-select" name="brand" class="publish-select">
                            <?php
                            $brands = Item::getBrands($db);
                            foreach ($brands as $brand) {
                                echo "<option value=\"{$brand}\">{$brand}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="removeBrand">Remove Brand</button>
                    </form>


                
                
                <?php endif; ?>
            </div>
            </div>
            
            
        </main>
    <?php
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

    