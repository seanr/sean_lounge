<?php

namespace Drupal\spotify_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\spotify_api\SpotifyApi;

/**
 * Provides an Artists block.
 *
 * @Block(
 *   id = "spotify_api_artists",
 *   admin_label = @Translation("Spotify Artists"),
 *   category = @Translation("Spotify API")
 * )
 */
class ArtistsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $spotify_api = new SpotifyApi(\Drupal::httpClient());
    $artists = [];

    // We have a set limit of 20 artists.
    for ($i = 1; $i <= 20; $i++) {
      if (isset($config['artist_' . $i]) && !empty($config['artist_' . $i])) {
        $artists[] = $config['artist_' . $i];
      }
    }

    $artist_data = $spotify_api->getArtists('artists/', ['query' => ['ids' => implode(',', $artists)]]);

    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $default = [];

    // We have a set limit of 20 artists.
    for ($i = 1; $i <= 20; $i++) {
      $default['artist_' . $i] = '';
    }
    return $default;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    // Create text fields for up to 20 artists.
    for ($i = 1; $i <= 20; $i++) {
      $form['artist_' . $i] = [
        '#type' => 'textfield',
        '#title' => $this->t('Artist ' . $i),
        '#default_value' => $this->configuration['artist_' . $i],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Save the values for each artist.
    for ($i = 1; $i <= 20; $i++) {
      $this->configuration['artist_' . $i] = $values['artist_' . $i];
    }
  }

}
