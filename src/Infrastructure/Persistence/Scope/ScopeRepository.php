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
use App\Infrastructure\Persistence\Scope\ScopeAdminRepositoryInterface;
use PDO;
use Psr\Log\LoggerInterface;

class ScopeRepository implements ScopeAdminRepositoryInterface
{
    private const SCOPES_TABLE = 'scopes';

    private LoggerInterface $logger;
    private PDO $pdo;

    public function __construct(LoggerInterface $logger, PDO $pdo)
    {
        $this->logger = $logger;
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

    /**
     * Return scopes
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes()
    {
        $query = 'SELECT * FROM ' . self::SCOPES_TABLE;
        $stmt  = $this->pdo->prepare($query);
        if ($stmt->execute()) {

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($scope) {

                $this->logger->debug('scope = ' . print_r($scope, true));

                $entity = new ScopeEntity();
                $entity->setIdentifier($scope['name']);
                return $entity;
            }, $results);
        }

        return [];
    }

    public function updateScope(ScopeEntity $scope)
    {
        $query = 'UPDATE ' . self::SCOPES_TABLE . ' SET identifier=?, description=? WHERE identifier=?';
        $stmt  = $this->pdo->prepare($query);
        if ($stmt->execute([$scope->getIdentifier(), $scope->getDescription(), $scope->getIdentifier()])) {
            return $scope;
        }
    }
}
