<?php

namespace Drupal\spotify_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
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
    $num_rows = FALSE;

    // Formatted text could contain an empty <p> tag.
    if (!empty($this->configuration['header']) && !empty(strip_tags($this->configuration['header']['value']))) {
      $build['header'] = [
        '#type' => 'processed_text',
        '#text' => $this->configuration['header']['value'],
        '#format' => $this->configuration['header']['format'],
      ];
    }

    // We have a set limit of 20 artists.
    for ($i = 1; $i <= 20; $i++) {
      if (isset($config['artist_' . $i]) && !empty($config['artist_' . $i])) {
        $artisst[] = $config['artist_' . $i];
      }
    }
    $artist_data = $spotify_api->getData('artists', ['query' => ['ids' => implode(',', $artists)]]);

    if ($artist_data) {
      $items = [];

      // Cycle through artist data.
      foreach ($artist_data->artists as $artist) {
        // Get the current user
        $user = \Drupal::currentUser();

        // Check for permission to view artist details page.
        if ($user->hasPermission('access spotify_api content')) {
          $items[] = Link::fromTextAndUrl($artist->name, Url::fromRoute('spotify_api.artist', ['artist' => $artist->id]))
            ->toString();
        }
        else {
          $items[] = $artist->name;
        }
        $num_rows = TRUE;
      }

      // If we have items, build the list.
      if ($num_rows) {
        $build['artists'] = [
          '#theme' => 'item_list',
          '#items' => $items,
        ];
      }
    }

    if (!$artist_data || !$num_rows) {
      // Display no results text.
      $build['no_results'] = [
        '#markup' => $this->t('No artists found.'),
      ];
    }

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
    $header = !empty($this->configuration['header']) ? $this->configuration['header'] : [];
    $form['header'] = [
      '#type' => 'text_format',
      '#title' => 'Block header',
      '#format' => isset($header['format']) ? $header['format'] : 'full_html',
      '#default_value' => isset($header['value']) ? $header['value'] : '',
    ];

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

    // Save the header text.
    $this->configuration['body'] = $values['body'];

    // Save the values for each artist.
    for ($i = 1; $i <= 20; $i++) {
      $this->configuration['artist_' . $i] = $values['artist_' . $i];
    }
  }

}
