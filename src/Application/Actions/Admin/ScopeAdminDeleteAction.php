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

class ScopeAdminDeleteAction extends Action
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

            if ('DELETE' == $this->request->getMethod()) {
                $this->scopeAdminRepositoryInterface->deleteScope($id);
                return $this->response->withStatus(200);
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
