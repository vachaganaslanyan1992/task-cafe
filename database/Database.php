<?php

namespace Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Returns a PDO connection instance.
     *
     * Reads DB config from environment variables.
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            // Load .env variables once
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->safeLoad();

            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $dbName = $_ENV['DB_DATABASE'] ?? 'task_cafe';
            $user = $_ENV['DB_USERNAME'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';
            $charset = 'utf8';

            $dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

            try {
                self::$connection = new PDO($dsn, $user, $pass);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
