<?php

declare(strict_types=1);

namespace App\Application\Actions\Login;

use App\Application\Actions\Action;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class LoginAction extends Action
{
    private Twig $twig;
    private UserRepositoryInterface $userRepo;

    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
        UserRepositoryInterface $userRepo
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
        $user = $this->userRepo->getUserEntityByUserCredentials(
            $username,
            $password,
            '',
            $client
        );
        if (null != $user) {

            $authRequest->setUser($user);
            $_SESSION['auth_request'] = $authRequest;

            return $this->response->withHeader('Location', '/authorize');
        }

        return $this->twig->render($this->response, 'login.html', ['error' => 'invalid credentials']);
    }
}
