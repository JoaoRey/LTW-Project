<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/review.class.php');
require_once(__DIR__ . '/../database/connection.db.php');
$session = new Session();
$db = getDatabaseConnection();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $session->addMessage('error', 'Invalid CSRF token');
        header('Location: ../pages');
        exit();
    }

    if (isset($_POST["user_id"]) && isset($_POST["review_text"]) && isset($_POST["rating"]) && isset($_POST["item_id"])) {

        $userId = intval($_POST["user_id"]);
        $reviewText = $_POST["review_text"];
        $rating = floatval($_POST["rating"]);
        $itemId = intval($_POST["item_id"]);

        

        try {
            Review::insertReview($db, $userId,$itemId, $rating, $reviewText);
            $session->addMessage("success", "Review processada com sucesso!");
            header("Location: /pages/postbought.php?id=$itemId");
            exit();
        } catch (Exception $e) {
            die("Erro ao processar a revisão: " . $e->getMessage());
        }
    } else {
        echo "Todos os campos são obrigatórios!";
    }
} else {
    header("Location: item_page.php?item_id=$itemId");
    exit();
}
?>
