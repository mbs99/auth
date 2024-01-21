<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\Client\ClientEntity;
use App\Domain\User\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use PDO;
use Psr\Log\LoggerInterface;
use App\Infrastructure\Persistence\User\UserAdminRepositoryInterface;
use App\Infrastructure\Persistence\Client\ClientRepository;

class UserRepository implements UserAdminRepositoryInterface
{
    const TABLE = 'users';

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
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $query = 'SELECT * FROM ' . self::TABLE . ' WHERE username = ? AND client_id = (SELECT id from ' . ClientRepository::CLIENTS_TABLE . ' WHERE identifier = ?)';
        $stmt  = $this->pdo->prepare($query);
        $stmt->bindParam(1, $username);
        $identifier = $clientEntity->getIdentifier();
        $stmt->bindParam(2, $identifier);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $result['password'])) {
                    return new UserEntity('' . $result['id']);
                }
            }
        }

        return;
    }

    /**
     * Get all users.
     *
     * @return UserEntityInterface[]
     */
    public function getUsers()
    {
        $query = 'SELECT ' . self::TABLE . '.*, ' . ClientRepository::CLIENTS_TABLE . '.identifier AS client_identifier FROM '
            . self::TABLE . ' LEFT JOIN ' . ClientRepository::CLIENTS_TABLE . ' ON ' . self::TABLE . '.client_id = ' . ClientRepository::CLIENTS_TABLE . '.id';
        $stmt  = $this->pdo->prepare($query);
        if ($stmt->execute()) {

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($result) {

                $this->logger->debug('user = ' . print_r($result, true));

                $entity = new UserEntity('' . $result['id']);
                $entity->setUsername($result['username']);
                $entity->setPassword('***');
                $clientEntity = new ClientEntity();
                $clientEntity->setIdentifier($result['client_identifier']);
                $entity->setClient($clientEntity);
                return $entity;
            }, $results);
        }

        return [];
    }

    /**
     * Create user.
     *
     * @return UserEntityInterface
     */
    public function createUser(UserEntity $user)
    {
    }

    /**
     * Update user.
     *
     * @return UserEntityInterface
     */
    public function updateUser(UserEntity $user)
    {
    }

    /**
     * Delete user.
     *
     * @return UserEntityInterface
     */
    public function deleteUser($id)
    {
    }

    /**
     * Get all users.
     *
     * @return UserEntityInterface|null
     */
    public function getUser($id)
    {
        $query = 'SELECT ' . self::TABLE . '.*, ' . ClientRepository::CLIENTS_TABLE . '.identifier AS client_identifier FROM '
            . self::TABLE . ' LEFT JOIN ' . ClientRepository::CLIENTS_TABLE . ' ON ' . self::TABLE . '.client_id = ' . ClientRepository::CLIENTS_TABLE . '.id'
            . ' where ' . ClientRepository::CLIENTS_TABLE . '.id = ' . $id;
        $stmt  = $this->pdo->prepare($query);
        if ($stmt->execute()) {

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->logger->debug('user = ' . print_r($result, true));

            $entity = new UserEntity('' . $result['id']);
            $entity->setUsername($result['namename']);
            $clientEntity = new ClientEntity();
            $clientEntity->setIdentifier($result['client_identifier']);
            $entity->setClient($clientEntity);
            return $entity;
        }

        return null;
    }
}
