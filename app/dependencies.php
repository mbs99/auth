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
        AuthorizationServer::class => function () {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();

            // Init pdo
            $server   = $_ENV["DB_URL"];
            $user     = $_ENV["DB_USER"];
            $password = $_ENV["DB_PASSWORD"];
            $options  = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            );
            $pdo = new PDO($server, $user, $password, $options);

            // Init our repositories
            $clientRepository = new ClientRepository($pdo);
            $scopeRepository = new ScopeRepository();
            $accessTokenRepository = new AccessTokenRepository();
            $authCodeRepository = new AuthCodeRepository();
            $refreshTokenRepository = new RefreshTokenRepository();

            $privateKeyPath = 'file://' . __DIR__ . '/../private.key';

            // Setup the authorization server
            $server = new AuthorizationServer(
                $clientRepository,
                $accessTokenRepository,
                $scopeRepository,
                $privateKeyPath,
                $_ENV["ENC_KEY"]
            );

            // Enable the authentication code grant on the server with a token TTL of 1 hour
            $server->enableGrantType(
                new AuthCodeGrant(
                    $authCodeRepository,
                    $refreshTokenRepository,
                    new \DateInterval('PT10M')
                ),
                new \DateInterval('PT1H')
            );

            return $server;
        }
    ]);
};
