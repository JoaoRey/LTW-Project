<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');


function drawSearchedProducts($session,$db, array $items, string $searchQuery) { ?>   
    <main id="main-search">
        <h1>Results for: <?= htmlspecialchars($searchQuery) ?></h1>
        <?php 
        $myId = $session->getId();
    
        $itemsToDisplay = array();
        if($session->isLoggedIn()) {
            $cartItems= Cart::getItemsByUser($db, $myId);
        
        foreach ($items as $item) {
            $foundInCart = false;
            foreach ($cartItems as $cartItem) {
                if ($item->itemId === $cartItem->itemId) {
                    $foundInCart = true;
                    break;
                }
            }
            if (!$foundInCart) {
                $itemsToDisplay[] = $item;
            }
        }
        } else {
            $itemsToDisplay = $items;
        }
        
        
        if (!empty($itemsToDisplay)) : ?>
            <?php foreach ($itemsToDisplay as $item) : 
                $image = Item::getItemImage($db, $item->itemId)[0]; 
                $condition = Item::getItemCondition($db, $item->itemId);
                ?>
                <a href="/pages/post.php?id=<?= $item->itemId ?>">
                    <article class="search-item">  
                    

                        
                        <div class="img-title-condition">
                            
                            <img class="search-img" src="../<?= $image->imageUrl ?>" alt="<?= $item->title ?>" style="width: 17em; height: 13em;">
                            <div class="title-cond">
                                <div>
                                    <h1><?= htmlspecialchars($item->title) ?></h1>
                                    <h2 class="date-search"><?= $item->listingDate ?></h2>
                                </div>
                                <p><strong>Condition:</strong> <?= htmlspecialchars($condition->conditionName) ?></p>
                            </div>
                        </div>
                
                        <p class="price-search"><?= number_format($item->price, 2) ?>€</p>
                </article>
             </a>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No items found for your search query.</p>
        <?php endif; ?>
    </main>
    
<?php } 

function drawFilteredProducts($session,$db, ?string $categoryName = null, ?string $brandName = null, ?string $condition = null, ?string $size = null, ?string $model = null, ?int $minPrice=0,?int $maxPrice=10000) { ?>   
    <main id="main-search">
        <h1>Results for: <?= htmlspecialchars($categoryName ?? 'All Categories') ?></h1>
        <?php
        
        if($minPrice == null){
            $minPrice = 0;
        }
        if($maxPrice == null){
            $maxPrice = 10000;
        }

        $categoryId = Item::getCategoryId($db, $categoryName);
        $conditionId = Item::getConditionId($db, $condition);
        $brandId = Item::getBrandId($db, $brandName);
        $sizeId = Item::getSizeId($db, $size);
        $modelId = Item::getModelId($db, $model);
        $items = Item::getAllActiveItems($db);
        if($categoryId !=0){
            $items = Item::filterItemsByCategoryId($db, $categoryId,$items);
        }
        if($brandId !=0){
            $items = Item::filterItemsByBrandId($db, $brandId, $items);
        }
        if($conditionId !=0){
            $items = Item::filterItemsByConditionId($db, $conditionId, $items);
        }
        if($sizeId !=0){
            $items = Item::filterItemsBySizeId($db, $sizeId, $items);
        }
        if($modelId !=0){
           $items = Item::filterItemsByModelId($db, $modelId, $items);
        }
        $myId = $session->getId();
    
        
        $itemsToDisplay = array();
        if($session->isLoggedIn()) {
            $cartItems= Cart::getItemsByUser($db, $myId);
        
        foreach ($items as $item) {
            $foundInCart = false;
            foreach ($cartItems as $cartItem) {
                if ($item->itemId === $cartItem->itemId) {
                    $foundInCart = true;
                    break;
                }
            }
            if (!$foundInCart) {
                $itemsToDisplay[] = $item;
            }
        }
        } else {
            $itemsToDisplay = $items;
        }
        
        if (!empty($itemsToDisplay)) : ?>
            <?php foreach ($itemsToDisplay as $item) : 
                
                if($item->price < $minPrice || $item->price > $maxPrice){
                    continue;
                }
                $image = Item::getItemImage($db, $item->itemId)[0]; 
                $conditionObj = Item::getItemCondition($db, $item->itemId);
                ?>
                <a href="/pages/post.php?id=<?= $item->itemId ?>">
                    <article class="search-item">  
                        <div class="img-title-condition">
                            <img  class="search-img" src="../<?= $image->imageUrl ?>" alt="<?= $item->title ?>" style="width: 17em; height: 13em;">
                            <div class="title-cond">
                                <div>
                                    <h1><?= htmlspecialchars($item->title) ?></h1>
                                    <h2 class="date-search"><?= $item->listingDate ?></h2>
                                </div>
                                <p><strong>Condition:</strong> <?= htmlspecialchars($conditionObj->conditionName) ?></p>
                            </div>
                        </div>
                        <p class="price-search"><?= number_format($item->price, 2) ?>€</p>
                    </article>
                </a>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No items found for this filter.</p>
        <?php endif; ?>
    </main>
<?php } 



