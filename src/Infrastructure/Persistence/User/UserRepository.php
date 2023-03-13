<?php

declare(strict_types=1);

namespace App\Domain\User;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        if ($username === 'alex' && $password === 'whisky') {
            return new UserEntity();
        }

        return;
    }
}
