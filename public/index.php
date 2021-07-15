<?php
/**
 * @author MaxFrecht <max.frecht@gmail.com>
 */
require_once __DIR__ . '/../vendor/autoload.php';
$pokedex = new \Maxime\BasicPokeapi\Pokedex();
//header('Content-Type: application/json');
try {
    echo json_encode($pokedex->getAllPokemons());
    //$pokedex->getAllPokemons();
} catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
    $e->getMessage();
}