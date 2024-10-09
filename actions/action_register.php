<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $session->addMessage('error', 'Invalid CSRF token');
        header('Location: ../pages');
        exit();
    }
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeatPassword = $_POST['repeat_password'];

    if ($password !== $repeatPassword) {
        $session->addMessage('error', 'Passwords do not match.');
        header("Location: /pages/register.php");
        exit();
    }

    $joinDate = date("Y-m-d"); 

    $address = $_POST['address'] !== '' ? $_POST['address'] : null;
    $city = $_POST['city'] !== '' ? $_POST['city'] : null;
    $district = $_POST['district'] !== '' ? $_POST['district'] : null;
    $country = $_POST['country'] !== '' ? $_POST['country'] : null;
    $postalCode = $_POST['postalCode'] !== '' ? $_POST['postalCode'] : null;
    $phone = $_POST['phone'] !== '' ? $_POST['phone'] : null;
    $imageUrl = $_POST['imageUrl'] !== '' ? $_POST['imageUrl'] : "database/uploads/default_user.png";
    $admin = false;

   

try {
    $userId = User::registerUser($db, $firstName, $lastName, $username, $email, $password, $joinDate, $address, $city, $district, $country, $postalCode, $phone, $imageUrl, $admin);
    
    $user = User::getUserWithPassword($db, $email, $password);
        
    if ($user) {
        $session->setId($user->userId);
        $session->setName($user->name());
        $session->addAdmin($user->admin);
        $session->addMessage('success', 'Register successful! You are now logged in.');
        header("Location: /");
        exit();
    } else {
        $session->addMessage('error', 'Login failed after registration.');
        header("Location: /pages/login.php");
        exit();
    }
    
} catch (Exception $e) {
    $emails = User::getAllEmails($db);
    $usernames = User::getAllUsernames($db);

    foreach ($emails as $email1) {
        if ($email === $email1) {
            $session->addMessage('error', 'Email already in use.');
            header("Location: /pages/register.php");
            exit();
        }
    }
    foreach ($usernames as $username1) {
        if ($username === $username1) {
            $session->addMessage('error', 'Username already in use.');
            header("Location: /pages/register.php");
            exit();
    }
    $session->addMessage('error', 'An error occurred while registering.');
    header("Location: /pages/register.php");
    exit();
}
}


} else {
    header("Location: /pages/register.php");
    exit();
}
?>
