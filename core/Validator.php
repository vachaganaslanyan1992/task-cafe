<?php

declare(strict_types=1);

namespace Core;

use Database\Database;
use PDO;

/**
 * Validator class to perform data validation with common rules.
 */
class Validator
{
    /**
     * @var array<string, mixed> Input data to validate
     */
    protected array $data = [];

    /**
     * @var array<string, string> Validation rules, e.g. ['email' => 'required|email|unique:users,email']
     */
    protected array $rules = [];

    /**
     * @var array<string, string[]> Validation errors collected during run
     */
    protected array $errors = [];

    /**
     * Static factory method to create and run the validator.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return self
     */
    public static function make(array $data, array $rules): self
    {
        $validator = new self();
        $validator->data = $data;
        $validator->rules = $rules;
        $validator->run();
        return $validator;
    }

    /**
     * Executes validation based on rules.
     *
     * @return void
     */
    protected function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            foreach ($rules as $rule) {
                $parts = explode(':', $rule);
                $method = 'validate_' . $parts[0];
                $param = $parts[1] ?? null;

                if (method_exists($this, $method)) {
                    $this->$method($field, $param);
                }
            }
        }
    }

    /**
     * Returns whether validation failed.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Returns collected validation errors.
     *
     * @return array<string, string[]>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Validates that a field is required (not empty after trim).
     *
     * @param string $field
     * @return void
     */
    protected function validate_required(string $field): void
    {
        if (empty(trim((string)($this->data[$field] ?? '')))) {
            $this->errors[$field][] = "Field '$field' is required.";
        }
    }

    /**
     * Validates that a field contains a valid email.
     *
     * @param string $field
     * @return void
     */
    protected function validate_email(string $field): void
    {
        if (!filter_var((string)($this->data[$field] ?? ''), FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "Field '$field' must be a valid email.";
        }
    }

    /**
     * Validates that a field value is unique in the database.
     * Format of $tableAndColumn: "table,column"
     *
     * @param string $field
     * @param string|null $tableAndColumn
     * @return void
     */
    protected function validate_unique(string $field, ?string $tableAndColumn): void
    {
        if ($tableAndColumn === null) {
            // Skip if no table and column specified
            return;
        }

        [$table, $column] = explode(',', $tableAndColumn);
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
        $stmt->execute([$this->data[$field] ?? '']);

        if ($stmt->fetchColumn() > 0) {
            $this->errors[$field][] = "Field '$field' must be unique.";
        }
    }
}
