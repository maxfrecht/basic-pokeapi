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
        $this->pokemons = [];
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
                $pokemonData['sprites']["other"]["dream_world"]["front_default"] ??
                $pokemonData['sprites']["other"]["official-artwork"]["front_default"] ?? 'not available',
                $pokemonData["sprites"]["front_default"] ?? 'not available'
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

    public function getAllPokemons(string $url = null): ?array
    {
        $response = $this->client->request('GET', $url ?? 'pokemon/');
        try {
            $data = $response->toArray();
            $pokemons = [];
            foreach($data["results"] as $pokemon) {
                try {
                    $pokemonId = explode('/', $pokemon["url"]);
                    $pokemonId = intval($pokemonId[6]);
                    $pokemons[] = $this->getPokemonById($pokemonId);
                } catch (TransportExceptionInterface $e) {
                    echo $e->getMessage();
                }
            }
            if(isset($data["next"])) {
                $newUrl = explode('v2/', $data["next"])[1];
                $pokemons = array_merge($pokemons, $this->getAllPokemons($newUrl));
            }
        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            echo $e->getMessage();
        }

        return $pokemons;
    }
}