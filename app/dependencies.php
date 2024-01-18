<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use App\Infrastructure\Persistence\Client\ClientRepository;
use App\Infrastructure\Persistence\Scope\ScopeRepository;
use App\Infrastructure\Persistence\AccessToken\AccessTokenRepository;
use App\Infrastructure\Persistence\AuthCode\AuthCodeRepository;
use App\Infrastructure\Persistence\RefreshToken\RefreshTokenRepository;
use Dotenv\Dotenv;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Slim\Views\Twig;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Middleware\ResourceServerMiddleware;
use App\Infrastructure\Persistence\Scope\ScopeAdminRepositoryInterface;
use App\Infrastructure\Persistence\AccessToken\AccessTokenAdminRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use App\Infrastructure\Persistence\Client\ClientAdminRepositoryInterface;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        PDO::class => function () {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();

            // Init pdo
            $server   = $_ENV["DB_URL"];
            $user     = $_ENV["DB_USER"];
            $password = $_ENV["DB_PASSWORD"];
            $options  = array();
            return new PDO($server, $user, $password, $options);
        },
        ClientAdminRepositoryInterface::class => function (ContainerInterface $c) {
            $pdo = $c->get(PDO::class);
            $logger = $c->get(LoggerInterface::class);
            return new ClientRepository($logger, $pdo);
        },
        ScopeAdminRepositoryInterface::class => function (ContainerInterface $c) {
            $pdo = $c->get(PDO::class);
            $logger = $c->get(LoggerInterface::class);
            return new ScopeRepository($logger, $pdo);
        },
        AccessTokenAdminRepositoryInterface::class => function (ContainerInterface $c) {
            $pdo = $c->get(PDO::class);
            return new AccessTokenRepository($pdo);
        },
        AuthCodeRepositoryInterface::class => function (ContainerInterface $c) {
            $pdo = $c->get(PDO::class);
            return new AuthCodeRepository($pdo);
        },
        RefreshTokenRepositoryInterface::class => function (ContainerInterface $c) {
            $pdo = $c->get(PDO::class);
            return new RefreshTokenRepository($pdo);
        },
        AuthorizationServer::class => function (ContainerInterface $c) {
            $privateKeyPath = 'file://' . __DIR__ . '/../private.key';

            // Setup the authorization server
            $server = new AuthorizationServer(
                $c->get(ClientAdminRepositoryInterface::class),
                $c->get(AccessTokenAdminRepositoryInterface::class),
                $c->get(ScopeAdminRepositoryInterface::class),
                $privateKeyPath,
                $_ENV["ENC_KEY"]
            );

            // Enable the authentication code grant on the server with a token TTL of 1 hour
            $server->enableGrantType(
                new AuthCodeGrant(
                    $c->get(AuthCodeRepositoryInterface::class),
                    $c->get(RefreshTokenRepositoryInterface::class),
                    new \DateInterval('PT10M')
                ),
                new \DateInterval('PT1H')
            );

            return $server;
        },
        ResourceServer::class => function (AccessTokenAdminRepositoryInterface $accessTokenAdminRepositoryInterface) {
            $publicKeyPath = 'file://' . __DIR__ . '/../public.key';

            $server = new ResourceServer(
                $accessTokenAdminRepositoryInterface,
                $publicKeyPath
            );

            return $server;
        },
        ResourceServerMiddleware::class => function (ResourceServer $resourceServer) {
            return new \League\OAuth2\Server\Middleware\ResourceServerMiddleware($resourceServer);
        },
        'view' => function () {
            return Twig::create(__DIR__ . '/../templates', ['cache' => __DIR__ . '/../var/cache']);
        }
    ]);
};
