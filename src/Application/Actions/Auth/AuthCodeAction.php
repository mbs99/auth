<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use League\OAuth2\Client\Provider\GenericProvider;

class AuthCodeAction extends Action
{
    private Twig $twig;
    private GenericProvider $provider;

    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
    ) {
        parent::__construct($logger);
        $this->twig = $container->get('view');
        $this->provider = new GenericProvider([
            'clientId'                => 'test',    // The client ID assigned to you by the provider
            'clientSecret'            => 'test',    // The client password assigned to you by the provider
            'redirectUri'             => 'https://auth.marc-stroebel.de/auth-code',
            'urlAuthorize'            => 'https://auth.marc-stroebel.de/authorize',
            'urlAccessToken'          => 'https://auth.marc-stroebel.de/access-token',
            'urlResourceOwnerDetails' => 'https://auth.marc-stroebel.de/user-details'
        ]);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        // If we don't have an authorization code then get one
        if (!isset($_GET['code'])) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $this->provider->getAuthorizationUrl();

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $this->provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || $_GET['state'] !== $_SESSION['oauth2state']) {

            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }

            exit('Invalid state');
        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $this->provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit($e->getMessage());
            }
        }

        header('Location: ' . '/');
        exit;
    }
}
