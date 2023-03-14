<?php

declare(strict_types=1);

use App\Application\Actions\Auth\AccessTokenAction;
use App\Application\Actions\Auth\AuthorizeAction;
use App\Application\Actions\Login\LoginViewAction;
use App\Application\Actions\Login\LoginAction;
use App\Application\Actions\Login\ScopeAction;
use App\Application\Actions\Login\ScopeViewAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->options(
        '/{routes:.*}',
        function (Request $request, Response $response) {
            // CORS Pre-Flight OPTIONS Request Handler
            return $response;
        }
    );

    $app->get(
        '/',
        function (Request $request, Response $response) {
            $response->getBody()->write('Hello world!');
            return $response;
        }
    );

    /*
    $app->group(
    '/users',
    function (Group $group) {
    $group->get('', ListUsersAction::class);
    $group->get('/{id}', ViewUserAction::class);
    }
    );
    */

    $app->get('/authorize', AuthorizeAction::class);

    $app->post('/access_token', AccessTokenAction::class);

    $app->post('/login', LoginAction::class);

    $app->get('/login', LoginViewAction::class);

    $app->get('/scopes', ScopeViewAction::class);

    $app->post('/scopes', ScopeAction::class);
};
