<?php

declare(strict_types=1);

namespace App\Application\Actions\Login;

use App\Application\Actions\Action;
use App\Infrastructure\Persistence\User\UserAdminRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class LoginAction extends Action
{
    private Twig $twig;
    private UserAdminRepositoryInterface $userRepo;

    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
        UserAdminRepositoryInterface $userRepo
    ) {
        parent::__construct($logger);
        $this->twig = $container->get('view');
        $this->userRepo = $userRepo;
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $authRequest = $_SESSION['auth_request'];

        $username =  $this->request->getParsedBody()['username'];
        $password = $this->request->getParsedBody()['password'];
        $client = $authRequest->getClient();

        $this->logger->debug(print_r($client, true));

        $user = $this->userRepo->getUserEntityByUserCredentials(
            $username,
            $password,
            '',
            $client
        );
        if (null != $user) {

            $authRequest->setUser($user);
            $_SESSION['auth_request'] = $authRequest;

            $scopes = $authRequest->getScopes();

            if (null == $scopes || empty($scopes)) {
                $_SESSION['is_approved'] = 'true';
                return $this->response->withHeader('Location', '/authorize')->withStatus(302);
            } else {
                return $this->response->withHeader('Location', '/scopes')->withStatus(302);
            }
        }

        return $this->twig->render($this->response, 'login.html', ['error' => 'invalid credentials']);
    }
}
