<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use App\Infrastructure\Persistence\Scope\ScopeAdminRepositoryInterface;
use App\Domain\Scope\ScopeEntity;

class ScopeAdminAction extends Action
{
    private Twig $twig;
    private ScopeAdminRepositoryInterface $scopeAdminRepositoryInterface;

    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($logger);
        $this->twig = $container->get('view');
        $this->scopeAdminRepositoryInterface = $container->get(ScopeAdminRepositoryInterface::class);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $authenticated = isset($_SESSION['oauth2token']);

        if ($authenticated) {
            $scopes = $this->scopeAdminRepositoryInterface->getScopes();

            $this->logger->debug('scopes = ' . print_r($scopes, true));

            if ('GET' == $this->request->getMethod()) {
                return $this->twig->render($this->response, 'admin_scopes.html', ['scopes' => $scopes]);
            } else if ('POST' == $this->request->getMethod()) {
                $body = $this->request->getParsedBody();
                $this->logger->debug('body = ' . print_r($body, true));

                $scope = new ScopeEntity();
                $scope->setIdentifier($body['identifier']);
                $scope->setDescription($body['description']);

                $scope = $this->scopeAdminRepositoryInterface->createScope($scope);

                $this->logger->debug('before redirect', [ScopeAdminAction::class]);

                $this->response->withHeader('HX-Redirect', '/admin/scopes')->withStatus(200);
            } else {
                return $this->twig->render($this->response, 'admin_scopes.html', ['scopes' => $scopes]);
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
