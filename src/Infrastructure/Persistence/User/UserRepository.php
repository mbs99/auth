<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use PDO;
use Psr\Container\ContainerInterface;

class UserRepository implements UserRepositoryInterface
{
    private const USERS_TABLE = 'users';

    private PDO $pdo;

    public function __construct(ContainerInterface $container)
    {
        $this->pdo = $container->get(PDO::class);
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
        $query = 'SELECT * FROM ' . self::USERS_TABLE . ' WHERE username = ? AND password = ? AND client_id = (SELECT id from clients WHERE identifier = ?)';
        $stmt  = $this->pdo->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $password);
        $identifier = $clientEntity->getIdentifier();
        $stmt->bindParam(3, $identifier);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                return new UserEntity('' . $result['username']);
            }
        }

        return;
    }
}
