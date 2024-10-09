<?php
declare(strict_types=1);

class User {
    public int $userId;
    public string $firstName;
    public string $lastName;
    public string $username;
    public string $email;
    public string $password;
    public string $joinDate;
    public ?string $address;
    public ?string $city;
    public ?string $district;
    public ?string $country;
    public ?string $postalCode;
    public ?string $phone;
    public ?string $imageUrl;
    public bool $admin;

    public function __construct(
        int $userId,
        string $firstName,
        string $lastName,
        string $username,
        string $email,
        string $password,
        string $joinDate,
        ?string $address,
        ?string $city,
        ?string $district,
        ?string $country,
        ?string $postalCode,
        ?string $phone,
        ?string $imageUrl,
        bool $admin
    )
    { 
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->joinDate = $joinDate;
        $this->address = $address;
        $this->city = $city;
        $this->district = $district;
        $this->country = $country;
        $this->postalCode = $postalCode;
        $this->phone = $phone;
        $this->imageUrl = $imageUrl;
        $this->admin = $admin;
    }

    // Get the full name of the user
    function name() {
        return $this->firstName . ' ' . $this->lastName;
      }
      
    // Get Username by ID
    static function getUserNameById(PDO $db, int $userId) : ?string {
        try {
            $stmt = $db->prepare('SELECT Username FROM User WHERE UserId = ?');
            $stmt->execute([$userId]);
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
                return $user['Username'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception("Error fetching username: " . $e->getMessage());
        }
    }
    //Get All Users
    static function getAllUsers (PDO $db) : array
{
    try {
        $stmt = $db->prepare('SELECT * FROM User');
        $stmt->execute();
        $users = [];
        while ($user = $stmt->fetch()) {
            $admin = (bool) $user['Admin'];
            $users[] = new User(
                $user['UserId'],
                $user['FirstName'],
                $user['LastName'],
                $user['Username'],
                $user['Email'],
                $user['Password'],
                $user['JoinDate'],
                $user['Address'],
                $user['City'],
                $user['District'],
                $user['Country'],
                $user['PostalCode'],
                $user['Phone'],
                $user['ImageUrl'],
                $admin
            );
        }
        return $users;
    } catch (PDOException $e) {
        throw new Exception("Error fetching users: " . $e->getMessage());
    }
}


    // Get a User
    static function getUser(PDO $db, int $id) : ?User {
        try {
            $stmt = $db->prepare('SELECT * 
                                  FROM User 
                                  WHERE UserId = ?');
            $stmt->execute([$id]);
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $admin = (bool) $user['Admin'];
            if ($user) {
                return new User(
                    $user['UserId'],
                    $user['FirstName'],
                    $user['LastName'],
                    $user['Username'],
                    $user['Email'],
                    $user['Password'],
                    $user['JoinDate'],
                    $user['Address'],
                    $user['City'],
                    $user['District'],
                    $user['Country'],
                    $user['PostalCode'],
                    $user['Phone'],
                    $user['ImageUrl'],
                    $admin
                );
            } else {
                return null; 
            }
        } catch (PDOException $e) {
            
            throw new Exception("Error fetching user: " . $e->getMessage());
            
        }
    }
    




    static function searchUsers(PDO $db, string $search, int $count) : array {
        try {
            $stmt = $db->prepare('SELECT UserId, FirstName, LastName, Username, Email, Password, JoinDate, Address, City, District, Country, PostalCode, Phone, ImageUrl, Admin FROM User WHERE FirstName LIKE ? OR LastName LIKE ? LIMIT ?');
            $stmt->execute([$search . '%', $search . '%', $count]);
    
            $users = [];

            while ($user = $stmt->fetch()) {
                $admin = (bool) $user['Admin'];
                $users[] = new User(
                    $user['UserId'],
                    $user['FirstName'],
                    $user['LastName'],
                    $user['Username'],
                    $user['Email'],
                    $user['Password'],
                    $user['JoinDate'],
                    $user['Address'],
                    $user['City'],
                    $user['District'],
                    $user['Country'],
                    $user['PostalCode'],
                    $user['Phone'],
                    $user['ImageUrl'],
                    $admin
                );
            }
    
            return $users;
        } catch (PDOException $e) {
            throw new Exception("Error searching users: " . $e->getMessage());
        }
    }

    // Get user with password
    static function getUserWithPassword(PDO $db, string $email, string $password) : ?User {
        try {
            $stmt = $db->prepare('SELECT UserId, FirstName, LastName, Username, Email, Password, JoinDate, Address, City, District, Country, PostalCode, Phone, ImageUrl, Admin FROM User WHERE lower(Email) = ?');
            $stmt->execute([strtolower($email)]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['Password'])) {
                $admin = (bool) $user['Admin'];
                return new User(
                    $user['UserId'],
                    $user['FirstName'],
                    $user['LastName'],
                    $user['Username'],
                    $user['Email'],
                    $user['Password'],
                    $user['JoinDate'],
                    $user['Address'],
                    $user['City'],
                    $user['District'],
                    $user['Country'],
                    $user['PostalCode'],
                    $user['Phone'],
                    $user['ImageUrl'],
                    $admin
                );
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }

    // Register a new user
    static function registerUser(PDO $db, string $firstName, string $lastName, string $username, string $email, string $password,string $joinDate, ?string $address, ?string $city, ?string $district, ?string $country, ?string $postalCode, ?string $phone, ?string $imageUrl, bool $admin) : string{
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO User (FirstName, LastName, Username, Email, Password, JoinDate, Address, City, District, Country, PostalCode, Phone, ImageUrl, Admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$firstName, $lastName, $username, $email, $hashedPassword, $joinDate, $address, $city, $district, $country, $postalCode, $phone, $imageUrl, $admin]);
    
            return $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error registering user: " . $e->getMessage());
        }
    }

    // Login a user
    static function loginUser(PDO $db, string $email, string $password) : bool {
        try {
            $user = User::getUserWithPassword($db, $email, $password);
            if ($user!=null) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new Exception("Error logging in user: " . $e->getMessage());
        }
    }


    // Get a user by ID
    static function getUserById(PDO $db, int $userId) : ?User {
        try {
            $stmt = $db->prepare('SELECT UserId, FirstName, LastName, Username, Email, Password, JoinDate, Address, City, District, Country, PostalCode, Phone, ImageUrl, Admin FROM User WHERE UserId = ?');
            $stmt->execute([$userId]);
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $admin = (bool) $user['Admin'];
            if ($user) {
                return new User(
                    $user['UserId'],
                    $user['FirstName'],
                    $user['LastName'],
                    $user['Username'],
                    $user['Email'],
                    $user['Password'],
                    $user['JoinDate'],
                    $user['Address'],
                    $user['City'],
                    $user['District'],
                    $user['Country'],
                    $user['PostalCode'],
                    $user['Phone'],
                    $user['ImageUrl'],
                    $admin
                );
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }  

    // Update a user
    static function updateUser(PDO $db, int $userId, string $firstName, string $lastName, string $username, string $email, string $password, ?string $address, ?string $city, ?string $district, ?string $country, ?string $postalCode, ?string $phone, ?string $imageUrl, bool $admin) : void {
        try {
            $stmt = $db->prepare('UPDATE User SET FirstName = ?, LastName = ?, Username = ?, Email = ?, Password = ?, Address = ?, City = ?, District = ?, Country = ?, PostalCode = ?, Phone = ?, ImageUrl = ?, Admin = ? WHERE UserId = ?');
            $stmt->execute([$firstName, $lastName, $username, $email, $password, $address, $city, $district, $country, $postalCode, $phone, $imageUrl, $admin, $userId]);
        } catch (PDOException $e) {
            throw new Exception("Error updating user: " . $e->getMessage());
        }
    }
    // Delete a user
    static function deleteUser(PDO $db, int $id) : void {
        try {
            $stmt = $db->prepare('DELETE FROM User WHERE UserId = ?');
            $stmt->execute(array($id));
        } catch (PDOException $e) {
            throw new Exception("Error deleting user: " . $e->getMessage());
        }
    }
  // Fetch presented products by user
  public static function fetchPresentedProducts(PDO $db, int $userId): array {
    try {
        $stmt = $db->prepare('
            SELECT ItemId, SellerId, Title, Description, Price, ListingDate
            FROM Item
            WHERE SellerId = ?
        ');
        $stmt->execute([$userId]);
        $products = [];

        while ($product = $stmt->fetch()) {
            $active = (bool) $product['Active'];
            $products[] = new Item(
                $product['ItemId'],
                $product['SellerId'],
                $product['Title'],
                $product['Description'],
                $product['Price'],
                $product['ListingDate'],
                $active
            );
        }

        return $products;
    } catch (PDOException $e) {
        throw new Exception("Error fetching presented products: " . $e->getMessage());
    }
}

// Elevate a user to admin
static function elevateUserToAdmin(PDO $db, int $userId) : void {
    try {
        $stmt = $db->prepare('UPDATE User SET Admin = 1 WHERE UserId = ?');
        $stmt->execute([$userId]);
    } catch (PDOException $e) {
        throw new Exception("Error elevating user to admin: " . $e->getMessage());
    }


}

// Verify if user is admin
static function isAdmin(PDO $db, int $userId) : bool {
    try {
        $stmt = $db->prepare('SELECT Admin FROM User WHERE UserId = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return (bool) $user['Admin'];
        } else {
            return false;
        }
    } catch (PDOException $e) {
        throw new Exception("Error fetching user: " . $e->getMessage());
    }
}


// Get all admins
static function getAllAdmins(PDO $db) : array {
    try {
        $stmt = $db->prepare('SELECT * FROM User WHERE Admin = 1');
        $stmt->execute();
        $users = [];

        while ($user = $stmt->fetch()) {
            $admin = (bool) $user['Admin'];
            $users[] = new User(
                $user['UserId'],
                $user['FirstName'],
                $user['LastName'],
                $user['Username'],
                $user['Email'],
                $user['Password'],
                $user['JoinDate'],
                $user['Address'],
                $user['City'],
                $user['District'],
                $user['Country'],
                $user['PostalCode'],
                $user['Phone'],
                $user['ImageUrl'],
                $admin
            );
        }

        return $users;
    } catch (PDOException $e) {
        throw new Exception("Error fetching admins: " . $e->getMessage());
    }


}

//get all emails
static function getAllEmails(PDO $db) : array {
    try {
        $stmt = $db->prepare('SELECT Email FROM User');
        $stmt->execute();
        $emails = [];
        while ($email = $stmt->fetch()) {
            $emails[] = $email['Email'];
        }
        return $emails;
    } catch (PDOException $e) {
        throw new Exception("Error fetching emails: " . $e->getMessage());
    }
}

// Get all usernames
static function getAllUsernames(PDO $db) : array {
    try {
        $stmt = $db->prepare('SELECT Username FROM User');
        $stmt->execute();
        $usernames = [];
        while ($username = $stmt->fetch()) {
            $usernames[] = $username['Username'];
        }
        return $usernames;
    } catch (PDOException $e) {
        throw new Exception("Error fetching usernames: " . $e->getMessage());
    }
}


// Update user address
static function updateUserAddress(PDO $db, int $userId, string $address,string $postalCode, string $city, string $district, string $country) : void {
    try {
        $stmt = $db->prepare('UPDATE User SET Address = ?,PostalCode=?, City = ?, District = ?, Country = ? WHERE UserId = ?');
        $stmt->execute([$address,$postalCode, $city, $district, $country, $userId]);
    } catch (PDOException $e) {
        throw new Exception("Error updating user address: " . $e->getMessage());
    }

}
}


?>