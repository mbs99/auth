<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserEntity;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;


interface UserAdminRepositoryInterface extends UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return UserEntityInterface[]
     */
    public function getUsers();

    /**
     * Create user.
     *
     * @return UserEntityInterface
     */
    public function createUser(UserEntity $user);

    /**
     * Update user.
     *
     * @return UserEntityInterface
     */
    public function updateUser(UserEntity $user);

    /**
     * Delete user.
     *
     * @return UserEntityInterface
     */
    public function deleteUser($id);

    /**
     * Get all users.
     *
     * @return UserEntityInterface|null
     */
    public function getUser($id);
}
