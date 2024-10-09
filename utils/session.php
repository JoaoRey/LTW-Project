  <?php
    class Session {
      private array $messages;

      public function __construct() {
        session_start();

        $this->messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
        unset($_SESSION['messages']);
        if (!isset($_SESSION['csrf_token'])) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
      }
      }

      public function isLoggedIn() : bool {
        return isset($_SESSION['id']);    
      }

      public function logout() {
        session_destroy();
      }
      public function getCsrfToken() : string {
        return $_SESSION['csrf_token'];
      }
      public function verifyCsrfToken(string $token) : bool {
        return hash_equals($_SESSION['csrf_token'], $token);
      }

      public function getId() : ?int {
        return isset($_SESSION['id']) ? $_SESSION['id'] : null;    
      }

      public function getName() : ?string {
        return isset($_SESSION['name']) ? $_SESSION['name'] : null;
      }

      public function setId(int $id) {
        $_SESSION['id'] = $id;
      }

      public function setName(string $name) {
        $_SESSION['name'] = $name;
      }

      public function addMessage(string $type, string $text) {
        $_SESSION['messages'][] = array('type' => $type, 'text' => $text);
      }

      public function getMessages() {
        return $this->messages;
      }
      public function isAdmin() : bool {
        return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
    }
    
      public function addAdmin(bool $admin) {
        $_SESSION['admin'] = $admin;
      }
    
    public function setMessage(string $text, string $type = 'info') {
      $_SESSION['messages'][] = array('type' => $type, 'text' => $text);
    }
  
  }
?>

