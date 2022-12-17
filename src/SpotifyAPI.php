<?php

namespace Drupal\spotify_api;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Utility\Error;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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

  /**
   * Get an access token for the Spotify API.
   *
   * @param $refresh
   *   Whether to forcibly refresh the access token.
   *
   * @return false|string
   *   The bearer token, or false on failure.
   */
  protected function getAccessToken($refresh = FALSE) {
    $config = \Drupal::config('spotify_api.settings');
    if (!$this->accessToken || $refresh) {
      try {
        $response = $this->client->request(
          'POST',
          'https://accounts.spotify.com/api/token',
          [
            'headers' => static::HEADERS,
            'form_params' => [
              'client_id' => $config->get('client_id'),
              'client_secret' => $config->get('client_secret'),
              'grant_type' => 'client_credentials',
            ],
          ]
        );
        $json = $response->getBody()->getContents();
        $this->accessToken = json_decode($json)->access_token;
      }
      // First try to catch the GuzzleException. This indicates a failed response from the remote API.
      catch (GuzzleException $error) {
        // Get the original response
        $response = $error->getResponse();
        // Get the info returned from the remote server.
        $response_info = $response->getBody()->getContents();
        // Using FormattableMarkup allows for the use of <pre/> tags, giving a more readable log item.
        $message = new FormattableMarkup('API connection error. Error details are as follows:<pre>@response</pre>', ['@response' => print_r(json_decode($response_info), TRUE)]);
        // Log the error
        $variables = Error::decodeException($error);
        \Drupal::logger('spotify_api remote')->error('%type: @message in %function (line %line of %file).', $variables);
        return false;
      }
      // A non-Guzzle error occurred. The type of exception is unknown, so a generic log item is created.
      catch (\Exception $error) {
        \Drupal::logger('spotify_api')->error($error->getMessage());
        return false;
      }

      $json = $response->getBody()->getContents();
      $this->accessToken = json_decode($json)->access_token;
    }
    return $this->accessToken;
  }

  /**
   * Get data from the Spotify API.
   *
   * @param $endpoint
   *   The endpoint to call.
   *
   * @param $options
   *   The options to pass to the request, generally just the query parameters.
   *
   * @return false|mixed
   *   The decoded json response from the API, or false on failure.
   */
  public function getData($endpoint, $options = []) {
    try {
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
    }
    catch (GuzzleException $error) {
      $response = $error->getResponse();
      $response_info = $response->getBody()->getContents();
      $message = new FormattableMarkup('API connection error. Error details are as follows:<pre>@response</pre>', ['@response' => print_r(json_decode($response_info), TRUE)]);
      $variables = Error::decodeException($error);
      \Drupal::logger('spotify_api remote')->error('%type: @message in %function (line %line of %file).', $variables);
      return false;
    }
    catch (\Exception $error) {
      \Drupal::logger('spotify_api')->error($error->getMessage());
      return false;
    }
    return json_decode($response->getBody()->getContents());
  }

}
