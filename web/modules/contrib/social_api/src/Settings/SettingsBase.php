<?php

namespace Drupal\social_api\Settings;

use Drupal\Core\Config\ImmutableConfig;

/**
 * Base class for implementor module settings.
 *
 * @package Drupal\social_api\Settings
 */
abstract class SettingsBase implements SettingsInterface {

  /**
   * The configuration object containing the data from the configuration form.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Creates a new settings object.
   *
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   The configuration object associated to the settings.
   */
  public function __construct(ImmutableConfig $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function factory(ImmutableConfig $config) {
    return new static($config);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig() {
    return $this->config;
  }

}
