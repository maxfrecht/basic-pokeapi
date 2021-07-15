<?php
/**
 * @author MaxFrecht <max.frecht@gmail.com>
 */
require_once __DIR__ . '/../vendor/autoload.php';
$pokedex = new \Maxime\BasicPokeapi\Pokedex();
header('Content-Type: application/json');
try {
    echo json_encode($pokedex->getPokemonById(550));
} catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
}