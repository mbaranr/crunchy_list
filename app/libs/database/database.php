<?php

/**
 * Function to establish a database connection using PDO.
 *
 * This function establishes a PDO connection to the database based on the configuration
 * provided in the database configuration file.
 *
 * @return PDO|null PDO object representing the database connection.
 */
function db()
{
    static $pdo; // Static variable to hold the PDO instance to ensure single connection

    if (! $pdo) {
        // Include the database configuration file
        $config = require './config/database.php';

        try {
            // Create a new PDO connection
            $pdo = new PDO(
                "{$config['driver']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
                $config['username'],
                $config['password']
            );
        } catch (PDOException $e) {
            // Display an error message if connection fails
            echo 'DB Error: ' . $e->getMessage();
        }
        
    }

    return $pdo;
}

/**
 * Execute a prepared SQL query and return the PDOStatement object.
 *
 * This function prepares and executes a SQL query using PDO based on the provided SQL string
 * and optional parameter bindings.
 *
 * @param string $sql SQL query string.
 * @param array $bindings Optional array of parameters and their values for query binding.
 * @return PDOStatement PDOStatement object representing the executed query.
 */
function query($sql, $bindings = [])
{
    $stmt = db()->prepare($sql); // Prepare the SQL query
    $stmt->execute($bindings); // Execute the query with optional bindings
    return $stmt; // Return the PDOStatement object
}

/**
 * Execute a prepared SQL query and fetch the first row from the result set.
 *
 * This function prepares and executes a SQL query using PDO, fetches the first row from
 * the result set, and returns it as an associative array.
 *
 * @param string $sql SQL query string.
 * @param array $bindings Optional array of parameters and their values for query binding.
 * @return mixed|null Associative array representing the first row of the result set, or null if no rows found.
 */
function fetch($sql, $bindings = [])
{
    return query($sql, $bindings)->fetch(); // Execute query and fetch the first row
}

/**
 * Execute a prepared SQL query and fetch all rows from the result set.
 *
 * This function prepares and executes a SQL query using PDO, fetches all rows from
 * the result set, and returns them as an array of associative arrays.
 *
 * @param string $sql SQL query string.
 * @param array $bindings Optional array of parameters and their values for query binding.
 * @return array Array of associative arrays representing all rows of the result set.
 */
function fetchAll($sql, $bindings = [])
{
    return query($sql, $bindings)->fetchAll(); // Execute query and fetch all rows
}

/**
 * Execute a prepared SQL query and return the number of affected rows.
 *
 * This function prepares and executes a SQL query using PDO, and returns the number of
 * rows affected by the query (for example, number of rows inserted, updated, or deleted).
 *
 * @param string $sql SQL query string.
 * @param array $bindings Optional array of parameters and their values for query binding.
 * @return int Number of rows affected by the query.
 */
function execute($sql, $bindings = [])
{
    return query($sql, $bindings)->rowCount(); // Execute query and return number of affected rows
}
