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
            $clients = $this->clientAdminRepositoryInterface->getClients();

            $this->logger->debug('clients = ' . print_r($clients, true), [ScopeAdminAction::class]);

            if ('GET' == $this->request->getMethod()) {

                $queryParams = $this->request->getQueryParams();

                if (is_array($queryParams && 'true' == $queryParams['edit'])) {
                    return $this->twig->render($this->response, 'admin_clients_edit.html', ['clients' => $clients]);
                } else if (is_array($queryParams && 'true' == $queryParams['cancel'])) {
                    return $this->twig->render($this->response, 'admin_clients_cancel.html', ['clients' => $clients]);
                } else
                    return $this->twig->render($this->response, 'admin_clients.html', ['clients' => $clients]);
            } else if ('POST' == $this->request->getMethod()) {
                $body = $this->request->getParsedBody();
                $this->logger->debug('body = ' . print_r($body, true), [ScopeAdminAction::class]);

                $client = new ClientEntity();
                $client->setIdentifier($body['identifier']);
                $client->setConfidential($body['is_confodential']);
                $client->setRedirectUri($body['redrect_uri']);
                $client->setName($body['name']);

                return $this->response->withHeader('HX-Redirect', '/admin/scopes')->withStatus(200);
            } else {
                return $this->response->withStatus(415);
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
