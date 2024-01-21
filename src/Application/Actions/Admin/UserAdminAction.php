<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use App\Infrastructure\Persistence\User\UserAdminRepositoryInterface;
use App\Domain\Client\ClientEntity;
use App\Domain\User\UserEntity;
use App\Infrastructure\Persistence\Client\ClientAdminRepositoryInterface;


class UserAdminAction extends Action
{
    private Twig $twig;
    private UserAdminRepositoryInterface $userAdminRepositoryInterface;
    private ClientAdminRepositoryInterface $clientAdminRepositoryInterface;

    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($logger);
        $this->twig = $container->get('view');
        $this->userAdminRepositoryInterface = $container->get(UserAdminRepositoryInterface::class);
        $this->clientAdminRepositoryInterface = $container->get(ClientAdminRepositoryInterface::class);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $authenticated = isset($_SESSION['oauth2token']);

        if ($authenticated) {

            $clients = $this->clientAdminRepositoryInterface->getClients();

            if ('GET' == $this->request->getMethod()) {

                $queryParams = $this->request->getQueryParams();
                $this->logger->debug('query = ' . print_r($queryParams, true), [$this::class]);

                $editMode = is_array($queryParams) && array_key_exists('edit', $queryParams);

                if ($editMode) {
                    if ('true' == $queryParams['edit']) {
                        $id = $this->resolveArg('id');
                        $user = $this->userAdminRepositoryInterface->getUser($id);
                        $this->logger->debug('user = ' . print_r($user, true), [$this::class]);
                        $this->logger->debug('edit = true');
                        return $this->twig->render($this->response, 'admin_users_edit.html', ['user' => $user, 'edit' => true, 'clients' => $clients]);
                    } else {
                        $id = $this->resolveArg('id');
                        $user = $this->userAdminRepositoryInterface->getUser($id);
                        $this->logger->debug('user = ' . print_r($user, true), [$this::class]);
                        $this->logger->debug('edit = false');
                        return $this->twig->render($this->response, 'admin_users_edit.html', ['user' => $user, 'edit' => false, 'clients' => $clients]);
                    }
                } else {
                    $users = $this->userAdminRepositoryInterface->getUsers();
                    $this->logger->debug('users = ' . print_r($users, true), [$this::class]);
                    return $this->twig->render($this->response, 'admin_users.html', ['users' => $users, 'clients' => $clients]);
                }
            } else if ('POST' == $this->request->getMethod()) {
                $body = $this->request->getParsedBody();
                $this->logger->debug('body = ' . print_r($body, true), [$this::class]);

                $user = new UserEntity($body['identifier']);
                $user->setUsername($body['username']);
                $user->setPassword($body['password']);
                $client = new ClientEntity();
                $client->setIdentifier($body['client_id']);
                $user->setClient($client);

                $this->userAdminRepositoryInterface->createUser($user);

                return $this->response->withHeader('HX-Redirect', '/admin/users')->withStatus(200);
            } else {
                return $this->response->withStatus(415);
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
