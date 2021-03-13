<?php

namespace Drupal\social_auth_facebook;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\social_auth\AuthManager\OAuth2Manager;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Contains all the logic for Facebook OAuth2 authentication.
 */
class FacebookAuthManager extends OAuth2Manager {

  /**
   * The Facebook client.
   *
   * @var \League\OAuth2\Client\Provider\Facebook
   */
  protected $client;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Used for accessing configuration object factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Used to get the authorization code from the callback request.
   */
  public function __construct(
    ConfigFactory $configFactory,
    LoggerChannelFactoryInterface $logger_factory,
    RequestStack $request_stack
  ) {
    parent::__construct(
      $configFactory->get('social_auth_facebook.settings'),
      $logger_factory,
      $this->request = $request_stack->getCurrentRequest());
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate() {
    try {
      $this->setAccessToken($this->client->getLongLivedAccessToken($this->client->getAccessToken('authorization_code',
        ['code' => $this->request->query->get('code')])));
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('social_auth_facebook')
        ->error('There was an error during authentication. Exception: ' . $e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUserInfo() {
    $access_token = $this->getAccessToken();
    if (!$this->user && $access_token != NULL) {
      $this->user = $this->client->getResourceOwner($access_token);
    }
    else {
      $this->loggerFactory->get('social_auth_facebook')
        ->error('There was an error fetching the access token for user.');
    }

    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthorizationUrl() {
    $scopes = ['email', 'public_profile'];

    $extra_scopes = $this->getScopes();
    if ($extra_scopes) {
      $scopes = array_merge($scopes, explode(',', $extra_scopes));
    }

    // Returns the URL where user will be redirected.
    return $this->client->getAuthorizationUrl([
      'scope' => $scopes,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function requestEndPoint($method, $path, $domain = NULL, array $options = []) {
    if (!$domain) {
      $domain = 'https://graph.facebook.com';
    }

    $url = $domain . '/v' . $this->settings->get('graph_version') . $path;
    $url .= '&access_token=' . $this->getAccessToken();

    $request = $this->client->getAuthenticatedRequest($method, $url, $this->getAccessToken(), $options);

    try {
      return $this->client->getParsedResponse($request);
    }
    catch (IdentityProviderException $e) {
      $this->loggerFactory->get('social_auth_facebook')
        ->error('There was an error when requesting ' . $url . '. Exception: ' . $e->getMessage());
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getState() {
    return $this->client->getState();
  }

}
