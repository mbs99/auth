<?php

declare(strict_types=1);

namespace App\Application\Actions\Login;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ScopeAction extends Action
{
    public function __construct(
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        if ($this->request->getParsedBody()['action'] == 'submit') {
            $_SESSION['is_approved'] = 'true';
        } else {
            $_SESSION['is_approved'] = 'false';
        }

        return $this->response->withHeader('Location', '/authorize')->withStatus(302);
    }
}
