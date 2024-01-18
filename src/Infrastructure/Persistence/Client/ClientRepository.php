<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace App\Infrastructure\Persistence\Client;

use App\Domain\Client\ClientEntity;
use PDO;
use App\Infrastructure\Persistence\Client\ClientAdminRepositoryInterface;
use Psr\Log\LoggerInterface;

class ClientRepository implements ClientAdminRepositoryInterface
{
    const CLIENTS_TABLE = 'clients';

    private LoggerInterface $logger;
    private $pdo;

    public function __construct(LoggerInterface $logger, PDO $pdo)
    {
        $this->logger = $logger;
        $this->pdo = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier)
    {
        $query = 'SELECT * FROM ' . self::CLIENTS_TABLE . ' WHERE identifier = ?';
        $stmt  = $this->pdo->prepare($query);
        $stmt->bindParam(1, $clientIdentifier);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $client = new ClientEntity();

            $client->setIdentifier($result['identifier']);
            $client->setName($result['name']);
            $client->setRedirectUri($result['redirect_uri']);
            if ($result['is_confidential']) {
                $client->setConfidential();
            }

            return $client;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $query = 'SELECT * FROM ' . self::CLIENTS_TABLE . ' WHERE identifier = ?';
        $stmt  = $this->pdo->prepare($query);
        $stmt->bindParam(1, $clientIdentifier);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (
                    $result['is_confidential'] === true
                    && \password_verify($clientSecret, $result['secret']) === false
                ) {
                    return false;
                }

                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getClients()
    {
        $query = 'SELECT * FROM ' . self::CLIENTS_TABLE;
        $stmt  = $this->pdo->prepare($query);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($client) {

                $this->logger->debug('client = ' . print_r($client, true));

                $entity = new ClientEntity();
                $entity->setIdentifier($client['identifier']);
                $entity->setName($client['name']);
                $entity->setRedirectUri($client['redirect_uri']);
                $entity->setConfidential($client['is_confidential']);
                return $entity;
            }, $results);
        }

        return [];
    }
}
