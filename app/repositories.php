<?php

declare(strict_types=1);


use DI\ContainerBuilder;
use App\Infrastructure\Persistence\User\UserAdminRepositoryInterface;
use App\Infrastructure\Persistence\User\UserRepository;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserAdminRepositoryInterface::class => \DI\autowire(UserRepository::class),
    ]);
};
