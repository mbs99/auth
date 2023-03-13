<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;
use App\Domain\Client\ClientRepository;
use App\Domain\AuthCode\AuthCodeRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use App\Domain\Scope\ScopeRepository;
use App\Domain\AccessToken\AccessTokenRepository;
use App\Domain\RefreshToken\RefreshTokenRepository;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true,
                // Should be set to false in production
                'logError' => false,
                'logErrorDetails' => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
            ]);
        }
    ]);
};