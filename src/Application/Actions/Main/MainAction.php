<?php

declare(strict_types=1);

namespace App\Application\Actions\Main;

use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use League\OAuth2\Client\Provider\GenericProvider;

class MainAction extends Action
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
            'clientId'                => 'XXXXXX',    // The client ID assigned to you by the provider
            'clientSecret'            => 'XXXXXX',    // The client password assigned to you by the provider
            'redirectUri'             => 'https://my.example.com/your-redirect-url/',
            'urlAuthorize'            => 'https://service.example.com/authorize',
            'urlAccessToken'          => 'https://service.example.com/token',
            'urlResourceOwnerDetails' => 'https://service.example.com/resource'
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

                // Optional, only required when PKCE is enabled.
                // Restore the PKCE code stored in the session.
                $this->provider->setPkceCode($_SESSION['oauth2pkceCode']);

                // Try to get an access token using the authorization code grant.
                $accessToken = $this->provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit($e->getMessage());
            }
        }

        return $this->twig->render($this->response, 'user-details.html', ['oauth_user_id' => $oauth_user_id]);
    }
}
