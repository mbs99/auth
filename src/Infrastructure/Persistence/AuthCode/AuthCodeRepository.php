<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace App\Infrastructure\Persistence\AuthCode;

use App\Domain\AuthCode\AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use PDO;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    private const TABLE = 'auth_codes';

    private const CLIENTS_TABLE = 'clients';
    private PDO $pdo;

    private const INSERT_QUERY = 'INSERT INTO ' . self::TABLE . '(id, identifier, user_id, client_id, redirect_uri, is_revoked, expiry_timestamp)'
        . ' VALUES (null, ?, ?, (SELECT id from clients where identifier = ?), ?, null, ?)';
    private const UPDATE_QUERY = 'UPDATE ' . self::TABLE . ' SET is_revoked = 1  where identifier = ?';
    private const SELECT_QUERY = 'SELECT is_revoked FROM ' . self::TABLE . ' where identifier = ?';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $stmt  = $this->pdo->prepare(self::INSERT_QUERY);
        $identifier = $authCodeEntity->getIdentifier();
        $stmt->bindParam(1, $identifier);
        $userId = $authCodeEntity->getUserIdentifier();
        $stmt->bindParam(2, $userId);
        $clientId = $authCodeEntity->getClient()->getIdentifier();

        $stmt->bindParam(3, $clientId);
        $redirectUri = $authCodeEntity->getRedirectUri();
        $stmt->bindParam(4, $redirectUri);
        $expiryTimestamp = $authCodeEntity->getExpiryDateTime()->getTimestamp();
        $stmt->bindParam(5, $expiryTimestamp);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        $stmt  = $this->pdo->prepare(self::UPDATE_QUERY);
        $stmt->bindParam(1, $codeId);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
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
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
}
