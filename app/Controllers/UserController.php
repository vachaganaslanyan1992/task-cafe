<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;
use Core\Validator;
use Core\Response;

/**
 * Controller responsible for user-related operations.
 */
class UserController
{
    private UserService $service;

    /**
     * UserController constructor.
     *
     * @param UserService|null $service Optional UserService instance
     */
    public function __construct(?UserService $service = null)
    {
        $this->service = $service ?? new UserService();
    }

    /**
     * Displays the main HTML page.
     *
     * @return void
     */
    public function index(): void
    {
        require_once __DIR__ . '/../../public/index.html';
    }

    /**
     * Stores a new user after validating the input data.
     *
     * @param array $request Request data (name, email)
     * @return void
     */
    public function store(array $request): void
    {
        $validator = Validator::make($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            Response::json(['error' => $validator->errors()], 200);
            return;
        }

        $this->service->createUser($request['name'], $request['email']);
        $users = $this->service->getUsers();

        Response::json(['users' => $users]);
    }

    /**
     * Resets (clears) all users.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->service->resetUsers();
        Response::json(['success' => true]);
    }
}
