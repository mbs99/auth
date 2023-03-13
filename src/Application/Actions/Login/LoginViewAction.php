<?php

declare(strict_types=1);

namespace App\Application\Actions\Login;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class LoginViewAction extends Action
{
    private Twig $twig;

    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($logger);
        $this->twig = $container->get('view');
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        return $this->twig->render($this->response, 'login.html', []);
    }
}
