<?php

namespace App\Infrastructure\Persistence\Scope;

use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;


interface ScopeAdminRepositoryInterface extends ScopeRepositoryInterface
{
    /**
     * Return scopes
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes();
}
