<?php

namespace Drupal\spotify_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\spotify_api\SpotifyAPI;

/**
 * Returns responses for Spotify API routes.
 */
class ArtistController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build($artist) {
    $spotify_api = new SpotifyApi(\Drupal::httpClient());
    $artist_data = $spotify_api->getData('artists/' . $artist);

    $build = [
      '#theme' => 'spotify_api_artist',
      '#link' => $artist_data->external_urls->spotify,
      '#name' => $artist_data->name,
      '#image_url' => $artist_data->images[0]->url,
      '#image_width' => $artist_data->images[0]->width,
      '#genres' => $artist_data->genres,
    ];

    return $build;
  }

}
