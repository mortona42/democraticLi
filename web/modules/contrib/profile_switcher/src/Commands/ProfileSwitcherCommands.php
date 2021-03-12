<?php

namespace Drupal\profile_switcher\Commands;

use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;

/**
 * A Drush commandfile for Profile Switcher module.
 */
class ProfileSwitcherCommands extends DrushCommands {

  /**
   * Switch Drupal profile in a installed site
   *
   * @param string $profile_to_install
   *   The profile to activate.
   *
   * @command switch:profile
   * @aliases sp,switch-profile
   */
  public function profile($profile_to_install) {
    $profile_to_remove = \Drupal::installProfile();

    $this->output()->writeln(dt("The site's install profile will be switched from !profile_to_remove to !profile_to_install.", [
      '!profile_to_remove' => $profile_to_remove,
      '!profile_to_install' => $profile_to_install,
    ]));
    if (!$this->io()->confirm(dt('Do you want to continue?'))) {
      throw new UserAbortException();
    }

    \Drupal::service('profile_switcher.profile_switcher')->switchProfile($profile_to_install);

    $this->output()->writeln('Profile changed to: ' . \Drupal::installProfile());
  }

}
