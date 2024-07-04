<?php
namespace App\Controllers;

class UserController
{
    private static $instance = null;
    private $logger;
    
    /**
     * Private constructor to prevent multiple instances.
     *
     * Initializes the LoggingController instance for logging purposes.
     */
    private function __construct() {
        $this->logger = LoggingController::getInstance();
    }
    
    /**
     * Get the single instance of the class.
     *
     * This method ensures that only one instance of UserController exists. If an instance is already
     * created, it will return that instance; otherwise, it will create a new one.
     *
     * @return UserController The single instance of the UserController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new UserController();
        }
        return self::$instance;
    }
    
    /* ----------------------------------------------------------------------------------------------------
     * Rendering
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Render the login or registration site.
     *
     * Depending on the value of $site parameter, it renders either the login or register view.
     *
     * @param string $site The site to render ('login' or 'register'). Defaults to 'login'.
     */
    public function index($site = 'login') {
        $logger = $this->logger;
        
        if(!($site == 'register')) {
            require_once __DIR__ . '/../views/login.php';
        } else {
            require_once __DIR__ . '/../views/register.php';
        }
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * User State Handling
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Handle user state transitions (logout, login, registration).
     *
     * Depending on the $route parameter, it handles logout, ensures login if not logged in,
     * handles login attempt, and handles registration attempt.
     *
     * @param string $route Reference to the current route, modified based on user state transitions.
     */
    public function handleUserState(&$route) {
        if ($route == 'logout') {
            $this->logout();
        }
        
        if (!$this->isLoggedIn()) {
            if ($route != 'register') {
                $route = 'login';
            }
        }
        
        if ($route == 'login' && isset($_POST['login'])) {
            $this->handleLogin($route);
        }
        
        if ($route == 'register' && isset($_POST['register'])) {
            $this->handleRegistration($route);
        }
    }
    
    /**
     * Handle user login attempt.
     *
     * Attempts to log in the user with provided username and password. Sets appropriate session variables on success.
     *
     * @param string $route Reference to the current route, modified to '/' after successful login.
     */
    private function handleLogin(&$route) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if ($this->login($username, $password)) {
            $_SESSION['loggedin'] = 'true';
            $route = '/'; // Redirect to home after successful login
        } else {
            $this->logger->setError('Invalid username or password.');
        }
    }
    
    /**
     * Handle user registration attempt.
     *
     * Validates user input for registration, registers the user if validation passes, and logs appropriate messages.
     *
     * @param string $route Reference to the current route, modified to 'login' after successful registration.
     */
    private function handleRegistration(&$route) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $repeat = $_POST['repeat'];
        
        if ($this->validateRegistration($username, $password, $repeat)) {
            if ($this->register($username, $password)) {
                $this->logger->setInfo('Registration successful.');
                $route = 'login'; // Redirect to login after successful registration
            } else {
                $this->logger->setError('Was not able to register user.');
            }
        }
    }
    
    /**
     * Validate user registration input.
     *
     * Validates username availability, password strength, and password match.
     *
     * @param string $username The username to validate.
     * @param string $password The password to validate.
     * @param string $repeat The repeated password to validate.
     * @return bool True if all validation passes, false otherwise.
     */
    private function validateRegistration($username, $password, $repeat) {
        if (empty($username) || strlen($username) < 4 || strlen($username) > 15) {
            $this->logger->setError('You must provide a username with 4 to 15 letters.');
            return false;
        }
        
        if (!$this->isUserNameAvailable($username)) {
            $this->logger->setError('Username not available. Try another one.');
            return false;
        }
        
        if (!$this->checkPasswordStrength($password)) {
            $this->logger->setError('The password must consist of at least 8 characters including letters, numbers and special characters.');
            return false;
        }
        if ($password !== $repeat) {
            $this->logger->setError('Passwords didn\'t match.');
            return false;
        }
        
        return true;
    }
    
    /* ----------------------------------------------------------------------------------------------------
     * Login/Logout Functionality
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Attempt to log in a user.
     *
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     * @return bool True if login is successful, false otherwise.
     */
    public function login($username, $password) {
        if ($username != '' && $password != '') {
            $sql = "SELECT *
                    FROM users
                    WHERE name = :username";
            $user = fetch($sql, ['username' => $username]);
            
            // If user exists check if password hash matches
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['username'] = $username;
                    $_SESSION['userID'] = $user['ID'];
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Check if a user is currently logged in.
     *
     * @return bool True if the user is logged in, false otherwise.
     */
    public function isLoggedIn() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 'true') {
            return true;
        }
        return false;
    }
    
    /**
     * Log out the current user.
     *
     * Unsets session variables and destroys the session.
     */
    public function logout() {
        $_SESSION['loggedin'] = 'false';
        session_destroy();
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Registration Functionality
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Check if a username is available for registration.
     *
     * @param string $username The username to check.
     * @return bool True if the username is available, false otherwise.
     */
    public function isUserNameAvailable($username) {
        $sql = "SELECT name
                FROM users
                WHERE name = :username";
        
        if(execute($sql, ['username' => $username]) == 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if a password meets the minimum strength requirements.
     *
     * Password must be at least 8 characters long and contain letters, numbers, and special characters.
     *
     * @param string $password The password to check.
     * @return bool True if the password meets the requirements, false otherwise.
     */
    public function checkPasswordStrength($password) {
        // Check if string has 8+ characters
        if (strlen($password) < 8) {
            return false;
        }
        
        // Check whether the string contains letters, numbers and special characters
        if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Register a new user.
     *
     * @param string $username The username of the new user.
     * @param string $password The password of the new user.
     * @return bool True if registration is successful, false otherwise.
     */
    public function register($username, $password) {
        $pwhash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, password)
                VALUES(:username, :password)";
        if(execute($sql, ['username' => $username, 'password' => $pwhash]) > 0) {
            return true;
        }
        
        return false;
    }
}
