<?php

namespace App\Infrastructure\Persistence\Scope;

use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use App\Domain\Scope\ScopeEntity;

interface ScopeAdminRepositoryInterface extends ScopeRepositoryInterface
{
    /**
     * Return scopes
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes();

    /**
     * update scope
     *
     * @return ScopeEntityInterface
     */
    public function updateScope(ScopeEntity $scope);

    /**
     * update scope
     *
     * @return ScopeEntityInterface
     */
    public function createScope(ScopeEntity $scope);
}
