<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Client;

use App\Domain\Client\ClientEntity;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

/**
 * Client storage interface.
 */
interface ClientAdminRepositoryInterface extends ClientRepositoryInterface
{
    /**
     * Get all clients.
     *
     *
     * @return ClientEntity[]
     */
    public function getClients();

    /**
     * Create client.
     *
     *
     * @return ClientEntity[]
     */
    public function createClient(ClientEntity $clientEntity);
}
