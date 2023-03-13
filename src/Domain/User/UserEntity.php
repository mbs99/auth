<?php

declare(strict_types=1);

namespace App\Domain\User;

use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    private string $identifier;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }
    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
