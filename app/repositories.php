<?php

declare(strict_types=1);


use DI\ContainerBuilder;
use App\Infrastructure\Persistence\User\UserAdminRepositoryInterface;
use App\Infrastructure\Persistence\User\UserRepository;
use Illuminate\Database\Capsule\Manager;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserAdminRepositoryInterface::class => \DI\autowire(UserRepository::class),
        Manager::class => function ($container) {
            $capsule = new \Illuminate\Database\Capsule\Manager;
            $capsule->addConnection($container['settings']['db']);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        }
    ]);
};
