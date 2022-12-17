<?php

namespace Drupal\spotify_api;

use GuzzleHttp\Client;

class SpotifyAPI {

  const HEADERS = [
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
  ];

  protected $client;
  protected $accessToken;

  public function __construct(Client $client) {
    $this->client = $client;
  }

  protected function getAccessToken($refresh = FALSE) {
    $config = \Drupal::config('spotify_api.settings');
    if (!$this->accessToken || $refresh) {
      $response = $this->client->request(
        'POST',
        'https://accounts.spotify.com/api/token',
        [
          'headers' => static::HEADERS + [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode($config->get('client_id') . ':' . $config->get('client_secret')),
          ],
          'body' => [
            'grant_type' => 'client_credentials',
          ],
        ]
      );
      $json = $response->getBody()->getContents();
      $this->accessToken = json_decode($json)->access_token;
    }
    return $this->accessToken;
  }

  public function getArtists($endpoint, $options = []) {
    $options = [
      'headers' => static::HEADERS + [
        'Authorization' => 'Bearer ' . $this->getAccessToken()
      ],
    ] + $options;
    $options = [
        'headers' => static::HEADERS,
      ] + $options;
    $response = $this->client->request(
      'GET',
      'https://api.spotify.com/v1/' . $endpoint,
      $options
    );
    return json_decode($response->getBody()->getContents());
  }

}
