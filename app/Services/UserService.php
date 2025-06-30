<?php

declare(strict_types=1);

namespace App\Services;

use Database\Database;
use PDO;

/**
 * Service class to handle user-related business logic.
 */
class UserService
{
    private PDO $pdo;

    /**
     * Constructor: initializes the PDO database connection.
     */
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Creates a new user and recalculates shares for all users.
     *
     * @param string $name User's name
     * @param string $email User's email
     * @return void
     */
    public function createUser(string $name, string $email): void
    {
        $count = (int)$this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $share = round(100 / ($count + 1), 2);

        $this->pdo->beginTransaction();

        // Update all existing users' share before inserting a new one
        $this->pdo->prepare("UPDATE users SET share = ?")->execute([$share]);

        // Insert the new user
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, share) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $share]);

        $this->pdo->commit();
    }

    /**
     * Deletes all users from the database.
     *
     * @return void
     */
    public function resetUsers(): void
    {
        $this->pdo->exec("DELETE FROM users");
    }

    /**
     * Returns all users sorted by ID descending.
     *
     * @return array Array of users
     */
    public function getUsers(): array
    {
        return $this->pdo
            ->query("SELECT * FROM users ORDER BY id DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
