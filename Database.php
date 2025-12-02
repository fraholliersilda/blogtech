<?php

class Database
{
    // Singleton instance to ensure only one connection
    private static ?PDO $connection = null;

    // Establish database connection
    public static function connect(string $host, string $databaseName, string $username, string $password): void
    {
        // Create connection only if it doesn't exist
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=$host;dbname=$databaseName";
                self::$connection = new PDO($dsn, $username, $password);
                // Set error mode to exception for better error handling
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }

    // Get the connection instance
    public static function getConnection(): PDO
    {
        // Throw exception if connection not established
        if (self::$connection === null) {
            throw new Exception("Database connection not established.");
        }
        return self::$connection;
    }
}

// Database credentials
$host = 'localhost';
$databaseName = 'users';
$username = 'root';
$password = '';

// Establish connection
Database::connect($host, $databaseName, $username, $password);

// Retrieve connection instance
$conn = Database::getConnection();