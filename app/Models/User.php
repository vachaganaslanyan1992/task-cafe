<?php

declare(strict_types=1);

namespace App\Models;

use Database\Database;
use PDO;

/**
 * User model for handling database operations related to users.
 */
class User
{
    /**
     * Returns all users in descending order by ID.
     *
     * @return array Array of users
     */
    public static function all(): array
    {
        $stmt = Database::getConnection()->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the total number of users.
     *
     * @return int Number of users
     */
    public static function count(): int
    {
        return (int)Database::getConnection()->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    /**
     * Recalculates and updates the share for all existing users
     * before inserting a new one.
     *
     * @return void
     */
    public static function recalculateSharesBeforeInsert(): void
    {
        $count = self::count();
        $newShare = round(100 / ($count + 1), 2);

        $stmt = Database::getConnection()->prepare("UPDATE users SET share = ?");
        $stmt->execute([$newShare]);
    }

    /**
     * Inserts a new user into the database with a calculated share.
     *
     * @param string $name User's name
     * @param string $email User's email
     * @return void
     */
    public static function create(string $name, string $email): void
    {
        $share = round(100 / (self::count() + 1), 2);

        $stmt = Database::getConnection()->prepare(
            "INSERT INTO users (name, email, share) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $email, $share]);
    }

    /**
     * Deletes all users from the database.
     *
     * @return void
     */
    public static function deleteAll(): void
    {
        Database::getConnection()->exec("DELETE FROM users");
    }
}
