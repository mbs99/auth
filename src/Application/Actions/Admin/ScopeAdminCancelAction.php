<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use App\Infrastructure\Persistence\Scope\ScopeAdminRepositoryInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class ScopeAdminCancelAction extends Action
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
            $id = $this->resolveArg('id');
            $scope = $this->scopeAdminRepositoryInterface->getScopeEntityByIdentifier($id);

            if ('GET' == $this->request->getMethod()) {
                return $this->twig->render($this->response, 'admin_scopes_cancel.html', ['scope' => $scope]);
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
