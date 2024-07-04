<?php
namespace App\Models;

class Genre
{
    private $id;
    private $name;
    
    /**
     * Constructor for Genre class.
     */
    public function __construct() {}
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Print formatted representation of the genre.
     */
    public function print() {
        echo 'id: ' . $this->id . '; name: ' . $this->name;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Getters
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get genre ID.
     *
     * @return int Genre ID.
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get genre name.
     *
     * @return string Genre name.
     */
    public function getName() {
        return $this->name;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Setters
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Set genre ID.
     *
     * @param int $id Genre ID.
     */
    public function setId($id) {
        if(is_int($id)) {
            $this->id = $id;
        }
    }
    
    /**
     * Set genre name.
     *
     * @param string $name Genre name.
     */
    public function setName($name) {
            $this->name = $name;
    }
}
