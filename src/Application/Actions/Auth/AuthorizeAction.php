<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\User\UserEntity;
use Laminas\Diactoros\Stream;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class AuthorizeAction extends Action
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
            // Validate the HTTP request and return an AuthorizationRequest object.
            // The auth request object can be serialized into a user's session
            if (!array_key_exists('auth_request', $_SESSION)) {
                $_SESSION['auth_request'] = $this->authorizationServer->validateAuthorizationRequest($this->request);
            }
            $authRequest = $_SESSION['auth_request'];

            if (null == $authRequest->getUser()) {
                return $this->response->withHeader('Location', '/login')->withStatus(302);
            }

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved($_SESSION['is_approved'] == 'true');

            // Return the HTTP redirect response
            return $this->authorizationServer->completeAuthorizationRequest($authRequest, $this->response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($this->response);
        } catch (\Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());

            return $this->response->withStatus(500)->withBody($body);
        }
    }
}
