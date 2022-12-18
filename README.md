# Spotify API Demo

This module is a demo of how to use the Spotify API to get information about artists and albums.

## Prerequisites

You will need to have a Spotify account and create a Spotify application to get a client ID and secret. See https://developer.spotify.com/documentation/general/guides/app-settings/#register-your-app

You do not need to add any redirect URIs to your application unless you intend to authenticate as your Spotify user for purposes of displaying your own data (this is not yet supported).

## Installation

1. Navigate to /admin/config/services/spotify-api and enter your Spotify API credentials.
2. Navigate to /admin/people/permissions and grant the "Access Spotify API content" permission to the appropriate roles.
3. Navigate to /admin/structure/blocks and add the Spotify Artists block to a region and configure it as desired.
4. Profit!

## About

This module was created by [seanr](https://www.drupal.org/u/seanr) to demonstrate the Spotify API.
