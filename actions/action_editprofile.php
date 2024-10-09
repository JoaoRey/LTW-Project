<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    die(header('Location: /'));
}

$db = getDatabaseConnection();
$user = User::getUser($db, $session->getId());

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $session->addMessage('error', 'Invalid CSRF token');
        header('Location: ../pages');
        exit();
    }

    $fieldsToUpdate = [];

    if (!empty($_POST['email'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            if (strlen($_POST['email']) <= 100) { 
                $fieldsToUpdate['Email'] = $_POST['email'];
            } else {
                $session->addMessage('error', 'Email must not exceed 100 characters');
                header('Location: ../pages/profilepage.php');
                exit();
            }
        } else {
            $session->addMessage('error', 'Invalid email format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }
    
    if (!empty($_POST['username'])) {
        if (preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s-]{1,20}$/', $_POST['username'])) {
            $fieldsToUpdate['Username'] = $_POST['username'];
        } else {
            $session->addMessage('error', 'Username must be 1-20 characters long');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }
    
    if (!empty($_POST['firstName'])) {
        if (preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s-]{1,50}$/', $_POST['firstName'])) {
            $fieldsToUpdate['FirstName'] = $_POST['firstName'];
        } else {
            $session->addMessage('error', 'Invalid first name format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }

    if (!empty($_POST['lastName'])) {
        if (preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s-]{1,50}$/', $_POST['lastName'])) {
            $fieldsToUpdate['LastName'] = $_POST['lastName'];
        } else {
            $session->addMessage('error', 'Invalid last name format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }
    if (!empty($_POST['address'])) {
        if(preg_match('/^[a-zA-Z0-9À-ÖØ-öø-ÿ\s,º.\'-]{1,100}$/', $_POST['address'])) {
            $fieldsToUpdate['Address'] = $_POST['address'];
        } else {
            $session->addMessage('error', 'Invalid address format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }
    if (!empty($_POST['city'])) {
        if (preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s-]{1,50}$/', $_POST['city'])) {
            $fieldsToUpdate['City'] = $_POST['city'];
        } else {
            $session->addMessage('error', 'Invalid city format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }

    if (!empty($_POST['district'])) {
        if (preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s-]{1,50}$/', $_POST['district'])) {
            $fieldsToUpdate['District'] = $_POST['district'];
        } else {
            $session->addMessage('error', 'Invalid district format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }

    if (!empty($_POST['country'])) {
        if (preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s-]{1,50}$/', $_POST['country'])) {
            $fieldsToUpdate['Country'] = $_POST['country'];
        } else {
            $session->addMessage('error', 'Invalid country format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }

    if (!empty($_POST['postalCode'])) {
        if (preg_match('/^\d{4}-\d{3}$/', $_POST['postalCode'])) {
            $fieldsToUpdate['PostalCode'] = $_POST['postalCode'];
        } else {
            $session->addMessage('error', 'Invalid postal code format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }

    if (!empty($_POST['phone'])) {
        if (preg_match('/^(\d{9}|\+\d{12})$/', $_POST['phone'])) {
            $fieldsToUpdate['Phone'] = $_POST['phone'];
        } else {
            $session->addMessage('error', 'Invalid phone number format');
            header('Location: ../pages/profilepage.php');
            exit();
        }
    }
    
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDirectory = '../database/uploads/';
        $imageUrls = [];
    
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if (!empty($_FILES['images']['name'][$key])) {
                $file_name = $_FILES['images']['name'][$key];
                $file_tmp = $_FILES['images']['tmp_name'][$key];
                $file_type = $_FILES['images']['type'][$key];
                
                $checkImage = getimagesize($file_tmp);
                if ($checkImage !== false) {
                    if ($file_type == 'image/jpeg' || $file_type == 'image/png') {
                        $userFolder = $uploadDirectory . 'user_' . $user->userId . '/';
                        if (!file_exists($userFolder)) {
                            mkdir($userFolder, 0777, true);
                        }
                        
                        $targetFile = $userFolder . $file_name;
                        
                        if (move_uploaded_file($file_tmp, $targetFile)) {
                            if (!empty($user->imageUrl)) {
                                unlink($user->imageUrl);
                            }
                            $fieldsToUpdate['ImageUrl'] = $targetFile;
                        } else {
                            $session->addMessage('error', 'Failed to upload image: ' . $file_name);
                        }
                    } else {
                        $session->addMessage('error', 'Only JPEG and PNG files are allowed: ' . $file_name);
                    }
                } else {
                    $session->addMessage('error', 'File is not a valid image: ' . $file_name);
                }
            }
        }
    }

    
    if (!empty($fieldsToUpdate)) {
        $setClause = '';
        foreach ($fieldsToUpdate as $field => $value) {
            $setClause .= "$field = :$field, ";
        }
        $setClause = rtrim($setClause, ', ');

        $sql = "UPDATE User SET $setClause WHERE UserId = :userId";
        $stmt = $db->prepare($sql);

        if ($stmt) {
            
            foreach ($fieldsToUpdate as $field => &$value) {
                $stmt->bindParam(":$field", $value);
            }
            $stmt->bindParam(':userId', $user->userId);

            if ($stmt->execute()) {
                $session->addMessage('success', 'Profile updated successfully');
                header('Location: ../pages/profilepage.php');
                exit();
            } else {
                $session->addMessage('error', 'Failed to update profile');
                header('Location: ../pages/errorfailedtoupdateprofile.php');
                exit();
            }
        } else {
            $session->addMessage('error', 'Failed to prepare SQL statement');
            header('Location: ../pages/errorsqlstatement.php');
            exit();
        }
    } else {
        // No fields to update, redirect back to profile page
        header('Location: ../pages/profilepage.php');
        exit();
    }
}
?>
