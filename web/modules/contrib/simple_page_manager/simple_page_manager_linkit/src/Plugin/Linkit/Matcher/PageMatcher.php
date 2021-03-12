<?php

namespace Drupal\simple_page_manager_linkit\Plugin\Linkit\Matcher;

use Drupal\linkit\Plugin\Linkit\Matcher\EntityMatcher;

/**
 * Provides specific linkit matchers for pages.
 *
 * @Matcher(
 *   id = "page",
 *   label = @Translation("Page"),
 *   target_entity = "page",
 * )
 */
class PageMatcher extends EntityMatcher {}
