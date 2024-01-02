<?php

declare(strict_types=1);

namespace App\Application\Actions\Main;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

use function PHPUnit\Framework\isEmpty;

class MainAction extends Action
{
    private Twig $twig;

    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
    ) {
        parent::__construct($logger);
        $this->twig = $container->get('view');
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $accessToken = $_SESSION['oauth2token'];
        return $this->twig->render($this->response, 'index.html', ['is_authenticated' => !isEmpty($accessToken)]);
    }
}
