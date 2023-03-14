<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace App\Infrastructure\Persistence\AccessToken;

use App\Domain\AccessToken\AccessTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use PDO;
use Psr\Container\ContainerInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    private const TABLE = 'access_tokens';
    private const INSERT_QUERY = 'INSERT INTO ' . self::TABLE . '(id, identifier, user_id, client_id, is_revoked)'
        . ' VALUES (null, ?, ?, (SELECT id from clients where identifier = ?), null)';
    private const UPDATE_QUERY = 'UPDATE ' . self::TABLE . ' SET is_revoked = 1  where identifier = ?';

    private const SELECT_QUERY = 'SELECT is_revoked FROM ' . self::TABLE . ' where identifier = ?';


    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $stmt  = $this->pdo->prepare(self::INSERT_QUERY);
        $identifier = $accessTokenEntity->getIdentifier();
        $stmt->bindParam(1, $identifier);
        $userId = $accessTokenEntity->getUserIdentifier();
        $stmt->bindParam(2, $userId);
        $clientId = $accessTokenEntity->getClient()->getIdentifier();
        $stmt->bindParam(3, $clientId);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $stmt  = $this->pdo->prepare(self::UPDATE_QUERY);
        $stmt->bindParam(1, $tokenId);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $stmt  = $this->pdo->prepare(self::SELECT_QUERY);
        $stmt->bindParam(1, $codeId);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['is_revoked'] == true;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }
}
