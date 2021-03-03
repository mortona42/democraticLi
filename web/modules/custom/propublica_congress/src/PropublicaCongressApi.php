<?php

namespace Drupal\propublica_congress;

use Drupal\Core\Http\ClientFactory;
use GuzzleHttp\ClientInterface;

/**
 * PropublicaCongressApi service.
 */
class PropublicaCongressApi {

  /**
   * The HTTP client factory.
   *
//   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClientFactory;

  /**
   * Constructs a PropublicaCongressApi object.
   *
//   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client factory.
   */
  public function __construct(ClientFactory $http_client_factory) {
    $this->httpClientFactory = $http_client_factory;
  }

  /**
   * Method description.
   */
  public function fetch() {
    $api = $this->httpClientFactory->fromOptions([
      'headers' => ['X-API-Key' => 'wBYIF2qm7LRTnGxEiQJln3DsoerquoWyQHhDAojU']
    ]);

    $results = $api->request('GET', 'https://api.propublica.org/congress/v1/116/senate/members.json');

    return $results->getBody()->getContents();

    $asdf ='asdf';
  }

}
