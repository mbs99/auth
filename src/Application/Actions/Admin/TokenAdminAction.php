<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use App\Infrastructure\Persistence\AccessToken\AccessTokenAdminRepositoryInterface;

class TokenAdminAction extends Action
{
    private Twig $twig;
    private AccessTokenAdminRepositoryInterface $accessTokenAdminRepositoryInterface;

    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($logger);
        $this->twig = $container->get('view');
        $this->accessTokenAdminRepositoryInterface = $container->get(AccessTokenAdminRepositoryInterface::class);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $authenticated = isset($_SESSION['oauth2token']);

        if ($authenticated) {
            $tokens = $this->accessTokenAdminRepositoryInterface->getAllTokens();

            $this->logger->debug('scopes = ' . print_r($tokens, true));

            if ('GET' == $this->request->getMethod()) {
                return $this->twig->render($this->response, 'admin_tokens.html', ['tokens' => $tokens]);
            } else if ('POST' == $this->request->getMethod()) {
            } else {
                return $this->twig->render($this->response, 'admin_tokens.html', ['tokens' => $tokens]);
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
