<?php


namespace Maxime\BasicPokeapi;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Pokedex
{
    private HttpClientInterface $client;

//    private array $pokemons;

    public function __construct()
    {
        $this->client = HttpClient::createForBaseUri('https://pokeapi.co/api/v2/');
    }

    /**
     * @return Pokemon|null
     * @throws TransportExceptionInterface
     */
    public function getPokemonById(int $id): ?array
    {
        $response = $this->client->request('GET', 'pokemon/' . $id);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Error from Pokeapi.co');
        }

        try {
            $pokemonData = $response->toArray();
            $pokemon = new Pokemon(
                $pokemonData['id'],
                $pokemonData['name'],
                $pokemonData['weight'],
                $pokemonData['base_experience'],
                $pokemonData["sprites"]["front_default"] ?? 'not available',
                $pokemonData['sprites']["other"]["dream_world"]["front_default"] ??
                $pokemonData['sprites']["other"]["official-artwork"]["front_default"] ?? 'not available'
            );
            return $pokemon->toArray();
        } catch (
        ClientExceptionInterface |
        DecodingExceptionInterface |
        RedirectionExceptionInterface |
        ServerExceptionInterface |
        TransportExceptionInterface $e
        ) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getAllPokemons(int $offset = 0, int $limit = 300): ?array
    {
        //Get response
        try {
            $response = $this->client->request('GET', 'pokemon/', [
                'query' => [
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);
        } catch (TransportExceptionInterface $e) {
            echo $e->getMessage();
        }


        try {
            if (!empty($response)) {
                $data = $response->toArray();
            }
            $pokemons = [];
            if (isset($data)) {

                foreach ($data["results"] as $pokemon) {
                    try {
//                        $pokemonId = explode('/', $pokemon["url"]);
//                        $pokemonId = intval($pokemonId[6]);
                        if (!preg_match('/([0-9]+)\/?$/', $pokemon['url'], $matches)) {
                            throw new \RuntimeException('Cannot match given url for pokemon ' . $pokemon['name']);
                        }
                        $pokemonId = intval($matches[0]);
                        $pokemons[] = $this->getPokemonById($pokemonId);
                    } catch (TransportExceptionInterface $e) {
                        echo $e->getMessage();
                    }
                }
            }

            //Get next pokemons recursively if page exists
            if (isset($data["next"])) {
                if (!preg_match('/\?.*offset=([0-9]+)/', $data['next'], $matches)) {
                    throw new \RuntimeException('Cannot match offset on next page.');
                }

                $nextOffset = $matches[1];

                $nextPokemons = $this->getAllPokemons($nextOffset);

                $pokemons = array_merge($pokemons, $nextPokemons);
            }
        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            echo $e->getMessage();
        }

        if (isset($pokemons)) {
            return $pokemons;
        }
        return null;
    }
}