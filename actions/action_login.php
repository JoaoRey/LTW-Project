<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

if (isset($_POST['email'], $_POST['password'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $session->addMessage('error', 'Invalid CSRF token');
        header('Location: ../pages');
        exit();
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $session->addMessage('error', 'Invalid email format');
        header('Location: ../pages/login.php');
        exit();
    }

    $passwordPattern = '/^(?=.*[!@#$%^&*?.])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*?]{6,}$/';
    if (!preg_match($passwordPattern, $_POST['password'])) {
      $session->addMessage('error', 'Passwords must be at least 6 characters long and contain at least one special character and one uppercase letter');
      header('Location: ../pages/login.php');
        exit();
    }

    $user = User::getUserWithPassword($db, $_POST['email'], $_POST['password']);

    if ($user) {
        $session->setId($user->userId);
        $session->setName($user->name());
        $session->addAdmin($user->admin);
        $session->addMessage('success', 'Login successful!');
        header('Location: /'); 
        exit();
    } else {
        $session->addMessage('error', 'Wrong email or password!');
    }
} else {
    $session->addMessage('error', 'Invalid form submission!');
}

header('Location: ../pages/login.php'); 
exit();
?>
