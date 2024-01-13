<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use App\Infrastructure\Persistence\AccessToken\AccessTokenAdminRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class TokenAdminAction extends Action
{
    private Twig $twig;
    private AccessTokenAdminRepositoryInterface $accessTokenAdminRepositoryInterface;
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($logger);
        $this->twig = $container->get('view');
        $this->accessTokenAdminRepositoryInterface = $container->get(AccessTokenAdminRepositoryInterface::class);
        $this->userRepositoryInterface = $container->get(UserRepositoryInterface::class);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $authenticated = isset($_SESSION['oauth2token']);

        if ($authenticated) {
            $tokens = $this->accessTokenAdminRepositoryInterface->getAllTokens();

            $this->logger->debug('tokens = ' . print_r($tokens, true));

            $tokenUsers = array();
            foreach ($tokens as $token) {
                $tokenUsers[$token->getIdentifier()] = 'Test';
            }

            $this->logger->debug('tokenUsers = ' . print_r($tokenUsers, true));

            if ('GET' == $this->request->getMethod()) {
                return $this->twig->render($this->response, 'admin_tokens.html', ['tokens' => $tokens, 'tokenUsers' => $tokenUsers]);
            } else if ('POST' == $this->request->getMethod()) {
                return $this->twig->render($this->response, 'admin_tokens.html', ['tokens' => $tokens, 'tokenUsers' => $tokenUsers]);
            } else {
            }
        }

        return $this->response->withHeader('Location', '/')->withStatus(401);
    }
}
