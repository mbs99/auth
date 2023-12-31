<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Log\LoggerInterface;
use Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class AuthTokenMiddleware implements Middleware
{
    private LoggerInterface $logger;
    private ResourceServer $resourceServer;
    private ResponseFactoryInterface $responseFactoryInterface;

    public function __construct(LoggerInterface $logger, ResourceServer $resourceServer, ResponseFactoryInterface $responseFactoryInterface)
    {
        $this->logger = $logger;
        $this->resourceServer = $resourceServer;
        $this->responseFactoryInterface = $responseFactoryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $response = $this->responseFactoryInterface->createResponse();

        try {
            $request = $this->resourceServer->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
            // @codeCoverageIgnoreEnd
        }

        // Pass the request and response on to the next responder in the chain
        return $handler->handle($request);
    }
}
