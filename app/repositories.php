<?php

declare(strict_types=1);


use DI\ContainerBuilder;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\User\UserRepository;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserRepositoryInterface::class => \DI\autowire(UserRepository::class),
    ]);
};
