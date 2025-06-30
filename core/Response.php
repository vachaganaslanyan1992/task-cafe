<?php

declare(strict_types=1);

namespace Core;

/**
 * Core response helper class to return JSON responses.
 */
class Response
{
    /**
     * Sends a JSON response with given data and HTTP status code.
     *
     * @param array $data The response data to be encoded as JSON
     * @param int $status The HTTP status code (default: 200)
     * @return void
     */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
