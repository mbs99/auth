<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use Laminas\Diactoros\Stream;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class AccessTokenAction extends Action
{
    private AuthorizationServer $authorizationServer;

    public function __construct(LoggerInterface $logger, AuthorizationServer $authorizationServer)
    {
        parent::__construct($logger);
        $this->authorizationServer = $authorizationServer;
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            return $this->authorizationServer->respondToAccessTokenRequest($this->request, $this->response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($this->response);
        } catch (\Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());

            return $this->response->withStatus(500)->withBody($body);
        }
    }
}