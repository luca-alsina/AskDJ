<?php

namespace App\Service;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SpotifyService
{

    private string $accessToken;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        private readonly CacheInterface $cache
    ) {

        // Authentication with Spotify API using Client Credentials Flow

        // Getting access token from cache if exists (or not expired) or requesting a new one from Spotify API
        $this->accessToken = $this->cache->get(
            'spotify_access_token',
            static function (ItemInterface $item) use ($clientId, $clientSecret) : string {

            // Creating client to request Spotify API
            $client = new \GuzzleHttp\Client();
            // Requesting access token
            $response = $client->post('https://accounts.spotify.com/api/token', [
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                ]
            ]);
            $data = json_decode($response->getBody()->getContents());

            // Setting cache expiration time from Spotify API response
            $item->expiresAfter($data->expires_in);

            // Returning access token from Spotify API response
            return $data->access_token;
        });

    }

    /**
     * @throws GuzzleException
     */
    public function searchTrack(string $search) : array
    {
        // Creating client to request Spotify API
        $client = new \GuzzleHttp\Client();

        // Requesting tracks from Spotify API with search term
        $response = $client->get(
            uri: 'https://api.spotify.com/v1/search?type=track&q=' . urlencode($search),
            options: [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ]
            ]
        );

        // Returning tracks list from Spotify API response as array
        return json_decode($response->getBody()->getContents())->tracks->items;

    }

}