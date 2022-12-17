<?php

namespace Drupal\spotify_api\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Spotify API routes.
 */
class SpotifyArtistController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
