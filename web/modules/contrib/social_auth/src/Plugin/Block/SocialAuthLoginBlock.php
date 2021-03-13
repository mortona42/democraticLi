<?php

namespace Drupal\social_auth\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\social_api\Plugin\NetworkManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Social Auth Block for Login.
 *
 * @Block(
 *   id = "social_auth_login",
 *   admin_label = @Translation("Social Auth Login"),
 * )
 */
class SocialAuthLoginBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The network manager.
   *
   * @var \Drupal\social_api\Plugin\NetworkManager
   */
  private $networkManager;

  /**
   * Immutable configuration for social_auth.settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $socialAuthConfig;

  /**
   * SocialAuthLoginBlock constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ImmutableConfig $social_auth_config
   *   The Immutable configuration for social_oauth.settings.
   * @param \Drupal\social_api\Plugin\NetworkManager $network_manager
   *   The Social API network manager.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    ImmutableConfig $social_auth_config,
    NetworkManager $network_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->networkManager = $network_manager;
    $this->socialAuthConfig = $social_auth_config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')->get('social_auth.settings'),
      $container->get('plugin.network.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Add social network name to data passed to template.
    // @todo Move `route` and `img_path` settings to the Network declaration.
    $networks = $this->networkManager->getDefinitions();
    $social_networks = $this->socialAuthConfig->get('auth');
    foreach ($social_networks as $id => &$social_network) {
      $social_network['name'] = NULL;
      if (isset($networks[$id]) && isset($networks[$id]['social_network'])) {
        $social_network['name'] = $networks[$id]['social_network'];
      }
    }
    return [
      '#theme' => 'login_with',
      '#social_networks' => $social_networks,
    ];
  }

}
