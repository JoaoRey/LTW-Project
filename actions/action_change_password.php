<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/connection.db.php');



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $session = new Session();
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $session->addMessage('error', 'Invalid CSRF token');
        header('Location: ../pages');
        exit();
    }
    $db = getDatabaseConnection();
    $user = User::getUser($db, $session->getId());

    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $passwordPattern = '/^(?=.*[!@#$%^&*?])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*?]{6,}$/';
    if (!preg_match($passwordPattern, $currentPassword) || !preg_match($passwordPattern, $newPassword) || !preg_match($passwordPattern, $confirmPassword)) {
        $session->addMessage('error', 'Passwords must be at least 6 characters long and contain at least one special character and one uppercase letter');
        header('Location: ../pages');
        exit();
    }

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $session->addMessage('error', 'Please fill out all fields');
        header('Location: ../pages');
        exit();
    } else if ($newPassword !== $confirmPassword) {
        $session->addMessage('error', 'New password and confirm password do not match');
        header('Location: ../pages');
        
        exit();
        
    } else if (!password_verify($currentPassword, $user->password)) {
        $session->addMessage('error', 'Incorrect current password');
        header('Location: ../pages');
        exit();
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE User SET Password = :password WHERE UserId = :userId";
        $stmt = $db->prepare($sql);

        
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':userId', $user->userId);

            if ($stmt->execute()) {
                $session->addMessage('success', 'Password changed successfully');
                header('Location: ../pages');
                exit();
            } else {
                $session->addMessage('error', 'Failed to update password');
                header('Location: ../pages');
                exit();
            }
        
    }
}

function redirectToProfilePage() {
    $_SESSION['redirect_to_profile'] = true;
    header('Location: ../pages');
    exit();
}
?>
