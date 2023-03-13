<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace App\Infrastructure\Persistence\Scope;

use App\Domain\Scope\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PDO;

class ScopeRepository implements ScopeRepositoryInterface
{
    private const SCOPES_TABLE = 'scopes';

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($scopeIdentifier)
    {
        $query = 'SELECT * FROM ' . self::SCOPES_TABLE . ' WHERE name = ?';
        $stmt  = $this->pdo->prepare($query);
        $stmt->bindParam(1, $scopeIdentifier);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                $scope = new ScopeEntity();
                $scope->setIdentifier($result['name']);

                return $scope;
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        // Example of programatically modifying the final scope of the access token
        /* if ((int) $userIdentifier === 1) {
            $scope = new ScopeEntity();
            $scope->setIdentifier('email');
            $scopes[] = $scope;
        }*/

        return $scopes;
    }
}
