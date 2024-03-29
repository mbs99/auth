<?php

declare(strict_types=1);

use App\Application\Actions\Auth\AccessTokenAction;
use App\Application\Actions\Auth\AuthorizeAction;
use App\Application\Actions\Auth\CheckTokenAction;
use App\Application\Actions\Login\LoginViewAction;
use App\Application\Actions\Login\LoginAction;
use App\Application\Actions\Login\ScopeAction;
use App\Application\Actions\Login\ScopeViewAction;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Application\Actions\User\UserDetailsAction;
use App\Application\Middleware\AuthTokenMiddleware;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Auth\AuthCodeAction;
use App\Application\Actions\Main\MainAction;
use App\Application\Actions\Login\LogoutAction;
use App\Application\Actions\Admin\ScopeAdminAction;
use App\Application\Actions\Admin\TokenAdminAction;
use App\Application\Actions\Admin\ScopeAdminEditAction;
use App\Application\Actions\Admin\ScopeAdminCancelAction;
use App\Application\Actions\Admin\ScopeAdminDeleteAction;
use App\Application\Actions\Admin\ClientAdminAction;
use App\Application\Actions\Admin\UserAdminAction;

return function (App $app) {

    $authTokenMiddleware = new AuthTokenMiddleware(
        $app->getContainer()->get(LoggerInterface::class),
        $app->getContainer()->get(ResourceServer::class),
        $app->getResponseFactory()
    );

    $app->options(
        '/{routes:.*}',
        function (Request $request, Response $response) {
            // CORS Pre-Flight OPTIONS Request Handler
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
    $app->get('/', MainAction::class);

    $app->get('/authorize', AuthorizeAction::class);

    $app->post('/access-token', AccessTokenAction::class);

    $app->post('/login', LoginAction::class);

    $app->get('/login', LoginViewAction::class);

    $app->get('/scopes', ScopeViewAction::class);

    $app->post('/scopes', ScopeAction::class);

    $app->get('/token', CheckTokenAction::class)->add($authTokenMiddleware);

    $app->get('/user-details', UserDetailsAction::class)->add($authTokenMiddleware);

    $app->get('/auth-code', AuthCodeAction::class);

    $app->get('/logout', LogoutAction::class);

    $app->get('/admin/scopes', ScopeAdminAction::class);

    $app->post('/admin/scopes', ScopeAdminAction::class);

    $app->get('/admin/scopes/{id}/edit', ScopeAdminEditAction::class);

    $app->get('/admin/scopes/{id}/cancel', ScopeAdminCancelAction::class);

    $app->put('/admin/scopes/{id}', ScopeAdminEditAction::class);

    $app->delete('/admin/scopes/{id}', ScopeAdminDeleteAction::class);

    $app->get('/admin/tokens', TokenAdminAction::class);

    $app->post('/admin/tokens', TokenAdminAction::class);

    $app->delete('/admin/tokens/{id}', TokenAdminAction::class);

    $app->post('/admin/clients', ClientAdminAction::class);

    $app->get('/admin/clients[/{id}]', ClientAdminAction::class);

    $app->post('/admin/users', UserAdminAction::class);

    $app->get('/admin/users[/{id}]', UserAdminAction::class);

    $app->put('/admin/users/{id}', UserAdminAction::class);
};
