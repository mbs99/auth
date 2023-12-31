<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class LUserDetailsAction extends Action
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
        $params = $this->request->getServerParams();
        $oauth_user_id = $params['oauth_user_id'] ?? null;

        return $this->twig->render($this->response, 'user-details.html', ['oauth_user_id' => $oauth_user_id]);
    }
}
