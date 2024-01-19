<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use App\Infrastructure\Persistence\Client\ClientAdminRepositoryInterface;
use App\Domain\Client\ClientEntity;

class ClientAdminAction extends Action
{
    private Twig $twig;
    private ClientAdminRepositoryInterface $clientAdminRepositoryInterface;

    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($logger);
        $this->twig = $container->get('view');
        $this->clientAdminRepositoryInterface = $container->get(ClientAdminRepositoryInterface::class);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $authenticated = isset($_SESSION['oauth2token']);

        if ($authenticated) {

            if ('GET' == $this->request->getMethod()) {

                $queryParams = $this->request->getQueryParams();
                $this->logger->debug('query = ' . print_r($queryParams, true), [$this::class]);

                $editMode = is_array($queryParams) && array_key_exists('edit', $queryParams);

                if ($editMode) {
                    if ('true' == $queryParams['edit']) {
                        $id = $this->resolveArg('id');
                        $client = $this->clientAdminRepositoryInterface->getClientEntity($id);
                        $this->logger->debug('client = ' . print_r($client, true), [$this::class]);
                        $this->logger->debug('edit = true');
                        return $this->twig->render($this->response, 'admin_clients_edit.html', ['client' => $client, 'edit' => true]);
                    } else {
                        $id = $this->resolveArg('id');
                        $client = $this->clientAdminRepositoryInterface->getClientEntity($id);
                        $this->logger->debug('client = ' . print_r($client, true), [$this::class]);
                        $this->logger->debug('edit = false');
                        return $this->twig->render($this->response, 'admin_clients_edit.html', ['client' => $client, 'edit' => false]);
                    }
                } else {
                    $clients = $this->clientAdminRepositoryInterface->getClients();
                    $this->logger->debug('clients = ' . print_r($clients, true), [$this::class]);
                    return $this->twig->render($this->response, 'admin_clients.html', ['clients' => $clients]);
                }
            } else if ('POST' == $this->request->getMethod()) {
                $body = $this->request->getParsedBody();
                $this->logger->debug('body = ' . print_r($body, true), [$this::class]);

                $client = new ClientEntity();
                $client->setIdentifier($body['identifier']);
                $client->setConfidential(array_key_exists('is_confodential', $body));
                $client->setRedirectUri($body['redrect_uri']);
                $client->setName($body['name']);

                $this->clientAdminRepositoryInterface->createClient($client);

                return $this->response->withHeader('HX-Redirect', '/admin/scopes')->withStatus(200);
            } else {
                return $this->response->withStatus(415);
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
