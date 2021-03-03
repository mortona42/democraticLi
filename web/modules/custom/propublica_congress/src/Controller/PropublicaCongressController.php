<?php

namespace Drupal\propublica_congress\Controller;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Menu\LocalActionManagerInterface;
use Drupal\Core\Plugin\Context\LazyContextRepository;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\propublica_congress\PropublicaCongressApi;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Propublica Congress routes.
 */
class PropublicaCongressController extends ControllerBase {

  /**
   * The Propublica Congress API service.
   *
   * @var \Drupal\propublica_congress\PropublicaCongressApi
   */
  protected $propublicaCongressApi;

  public function __construct(PropublicaCongressApi $propublica_congress_api) {
    $this->propublicaCongressApi = $propublica_congress_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('propublica_congress.api')
    );
  }


  /**
   * Builds the response.
   */
  public function build() {

    $results = $this->propublicaCongressApi->fetch();

    $build['content'] = [
      '#type' => 'item',
      '#markup' => '<pre>' . $results . '</pre>',
    ];

    return $build;
  }

}
