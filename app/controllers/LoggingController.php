<?php
namespace App\Controllers;

class LoggingController 
{   
    private static $instance = null;
    private $info, $warning, $error;
    
    /**
     * Private constructor to prevent multiple instances.
     *
     * Initializes the info, warning, and error messages to empty strings.
     */
    private function __construct() {
        $this->info = '';
        $this->warning = '';
        $this->error = '';
    }
    
    /**
     * Get the single instance of the class.
     *
     * This method ensures that only one instance of LoggingController exists. If an instance is already
     * created, it will return that instance; otherwise, it will create a new one.
     *
     * @return LoggingController The single instance of the LoggingController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new LoggingController();
        }
        return self::$instance;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Handler Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Handle the logging of messages.
     *
     * This method checks if there are any info, warning, or error messages to log.
     * If there are, it logs them and then clears the respective messages.
     */
    public function handleLogs() {
        // Print info if necessary
        if($this->getInfo() != '') {
            $this->logInfo();
            $this->setInfo();
        }
        
        // Print warning if necessary
        if($this->getWarning() != '') {
            $this->logWarning();
            $this->setWarning();
        }
        
        // Print error if necessary
        if($this->getError() != '') {
            $this->logError();
            $this->setError();
        }
    }
    
    /* --------------------------------------------------
     * Logging Functions
     * -------------------------------------------------- */
    
    /**
     * Log an info message.
     *
     * @param string $message The message to log. If empty, it will log the current info message.
     */
    public function logInfo($message) {
        if(empty($message)) {
            $message = $this->getInfo();
        }
        $this->log($message);
    }
    
    /**
     * Log a warning message.
     *
     * @param string $message The message to log. If empty, it will log the current warning message.
     */
    public function logWarning($message) {
        if(empty($message)) {
            $message = $this->getWarning();
        }
        $this->log($message, 'warning');
    }
    
    /**
     * Log an error message.
     *
     * @param string $message The message to log. If empty, it will log the current error message.
     */
    public function logError($message) {
        if(empty($message)) {
            $message = $this->getError();
        }
        $this->log($message, 'error');
    }
    
    /**
     * Log a message with a specified level.
     *
     * @param string $message The message to log.
     * @param string $level The level of the log message ('info', 'warning', 'error'). Defaults to 'info'.
     */
    public function log($message, $level = 'info') {
        $textColor = 'bright-grey-text';
        
        if(empty($message)) {
            $message = $this->getInfo();
        }
        
        if($level == 'warning'){
            $textColor = 'orange-text';
        }
        if($level == 'error'){
            $textColor = 'red-text';
        }
        echo '<div class="container dark-bg ' . $textColor . ' text-center padding-v-15 round-border">' . $message . '</div>';
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Other/Helper Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Check if there is an info message.
     *
     * @return bool True if there is an info message, false otherwise.
     */
    public function hasInfo() {
        if($this->info != '') {
            return true;
        }
        return false;
    }
    
    /**
     * Check if there is a warning message.
     *
     * @return bool True if there is a warning message, false otherwise.
     */
    public function hasWarning() {
        if($this->warning != '') {
            return true;
        }
        return false;
    }
    
    /**
     * Check if there is an error message.
     *
     * @return bool True if there is an error message, false otherwise.
     */
    public function hasError() {
        if($this->error != '') {
            return true;
        }
        return false;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Getters and Setters
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get the current info message.
     *
     * @param bool $clear Whether to clear the info message after retrieving it. Defaults to true.
     * @return string The current info message.
     */
    public function getInfo($clear = true) {
        $message = $this->info;
        if($clear) {
            $this->setInfo();
        }
        return $message;
    }
    
    /**
     * Get the current warning message.
     *
     * @param bool $clear Whether to clear the warning message after retrieving it. Defaults to true.
     * @return string The current warning message.
     */
    public function getWarning($clear = true) {
        $message = $this->warning;
        if($clear) {
            $this->setWarning();
        }
        return $message;
    }
    
    /**
     * Get the current error message.
     *
     * @param bool $clear Whether to clear the error message after retrieving it. Defaults to true.
     * @return string The current error message.
     */
    public function getError($clear = true) {
        $message = $this->error;
        if($clear) {
            $this->setError();
        }
        return $message;
    }
    
    /**
     * Set the info message.
     *
     * @param string $message The message to set as info. Defaults to an empty string.
     */
    public function setInfo($message = '') {
        $this->info = $message;
    }
    
    /**
     * Set the warning message.
     *
     * @param string $message The message to set as warning. Defaults to an empty string.
     */
    public function setWarning($message = '') {
        $this->warning = $message;
    }
    
    /**
     * Set the error message.
     *
     * @param string $message The message to set as error. Defaults to an empty string.
     */
    public function setError($message = '') {
        $this->error = $message;
    }

}
