<?php

declare(strict_types=1);
require_once(__DIR__ . '/../utils/session.php');


$session = new Session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    if (!$session->verifyCsrfToken($csrf_token)) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Erro de CSRF: Token CSRF inválido."]);
        exit;
    }
}

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
$db = getDatabaseConnection();
function getBrands($db) {
    
    $brands = Item::getBrands($db);

    header('Content-Type: application/json');
    echo json_encode($brands);
}


function getConditions($db) {
    
    $conditions = Item::getConditions($db);


    header('Content-Type: application/json');
    echo json_encode($conditions);
}


function getSizes($db) {
    
    $sizes = Item::getSizes($db);


    header('Content-Type: application/json');
    echo json_encode($sizes);
}

function getModels($db) {
    $models = Item::getModels($db);

    
    header('Content-Type: application/json');
    echo json_encode($models);
}
function getCategories($db) {
    $categories = Item::getCategories($db);

    
    header('Content-Type: application/json');
    echo json_encode($categories);
}
function removeCategory($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeCategory'])) {
        
        
        $categoryToRemove = $_POST['category'];
        $result = Item::removeCategory($db, $categoryToRemove);
        $session = new Session();

        if ($result==0) {
            $session->addMessage('success', 'Category removed successfully!');
            header('Location: /pages/profilepage.php');
        } else {
            $session->addMessage('error', 'Error removing category!');
            header('Location: /pages/profilepage.php');
        }
    }
    header('Location: /../pages/profilepage.php');
}
function removeModel($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeModel'])) {
        $modelToRemove = $_POST['model'];
        $result = Item::removeModel($db, $modelToRemove);
        $session = new Session();

        if ($result==0) {
            $session->addMessage('success', 'Model removed successfully!');
        } else {
            $session->addMessage('error', 'Error removing model!');
        }
    }
    header('Location: /../pages/profilepage.php');
}
function removeSize($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeSize'])) {
        $sizeToRemove = $_POST['size'];
        // Chame o método removeSize da classe Item passando a conexão do banco de dados e o nome do tamanho a ser removido
        $result = Item::removeSize($db, $sizeToRemove);
        $session = new Session();

        if ($result==0) {
            $session->addMessage('success', 'Size removed successfully!');
        } else {
            $session->addMessage('error', 'Error removing size!');
        }
    }
    header('Location: /../pages/profilepage.php');
}
function removeCondition($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeCondition'])) {
        $conditionToRemove = $_POST['condition'];
        $result = Item::removeCondition($db, $conditionToRemove);
        $session = new Session();

        if ($result==0) {
            $session->addMessage('success', 'Condition removed successfully!');
        } else {
            $session->addMessage('error', 'Error removing condition!');
        }
        
    }
    header('Location: /../pages/profilepage.php');
}
function removeBrand($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeBrand'])) {
        $brandToRemove = $_POST['brand'];
        $result = Item::removeBrand($db, $brandToRemove);
        $session = new Session();

        if ($result==0) {
            $session->addMessage('success', 'Brand removed successfully!');
        } else {
            $session->addMessage('error', 'Error removing brand!');
        }
    }
    header('Location: /../pages/profilepage.php');
}




if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['removeCategory'])) {
        removeCategory($db);
    }
    elseif (isset($_POST['removeModel'])) {
        removeModel($db);
    }
    elseif (isset($_POST['removeSize'])) {
        removeSize($db);
    }
    elseif (isset($_POST['removeCondition'])) {
        removeCondition($db);
    }
    elseif (isset($_POST['removeBrand'])) {
        removeBrand($db);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $db = getDatabaseConnection();
    $action = $_GET['action'];
    
    if ($action === 'get-brands') {
        getBrands($db);
    } elseif ($action === 'get-conditions') {
        getConditions($db);
    } elseif ($action === 'get-sizes') {
        getSizes($db);
    } elseif ($action === 'get-models') {
        getModels($db);
    } elseif ($action === 'get-categories') {
        getCategories($db);
        
    }else {
        http_response_code(404);
        echo "Not Found";
    } 
}
else {
http_response_code(404);
echo "Not Found request method or action";
}
?>
