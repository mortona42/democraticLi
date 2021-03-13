<?php

namespace Drupal\social_auth\Event;

use Drupal\Component\EventDispatcher\Event as Drupal9Event;
use Symfony\Component\EventDispatcher\Event as Drupal8Event;

/**
 * Determine Event class to extend from based on Symfony 3/4 availability.
 *
 * Drupal 9 uses Symfony 4, which has moved the Event class to a new namespace.
 * In order to support both Drupal 8 and 9, this class created a proxy based on
 * which of the Symfony classes is available.
 */
if (class_exists(Drupal9Event::class)) {
  /**
   * Extends from Drupal 9/Symfony 4 Event class.
   *
   * @package Drupal\social_auth\Event
   */
  class EventProxy extends Drupal9Event {}
}
elseif (class_exists(Drupal8Event::class)) {
  /**
   * Extends from Drupal8/Symfony 3 Event class.
   *
   * @package Drupal\social_auth\Event
   */
  class EventProxy extends Drupal8Event {}
}

/**
 * Base class for the Social Auth events.
 *
 * @see \Drupal\social_auth\Event\SocialAuthEvents
 */
abstract class SocialAuthEventBase extends EventProxy {

}
