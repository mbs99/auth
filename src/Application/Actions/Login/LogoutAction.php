<?php

declare(strict_types=1);

namespace App\Application\Actions\Login;

use App\Application\Actions\Action;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class LogoutAction extends Action
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
        $_SESSION = [];

        return $this->twig->render($this->response, 'index.html', ['is_authenticated' => false]);
    }
}
