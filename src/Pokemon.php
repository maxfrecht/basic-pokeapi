<?php


namespace Maxime\BasicPokeapi;


use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Pokemon
 * @package Maxime\BasicPokeapi
 */
class Pokemon
{
    private string $id;
    private string $name;
    private int $weight;
    private int $base_exp;
    private string $artwork;
    private string $sprite;

    /**
     * Pokemon constructor.
     * @param string $id
     * @param string $name
     * @param int $weight
     * @param int $base_exp
     * @param string $artwork
     * @param string $sprite
     */
    public function __construct(string $id, string $name, int $weight, int $base_exp, string $artwork, string $sprite)
    {
        $this->id = $id;
        $this->name = $name;
        $this->weight = $weight;
        $this->base_exp = $base_exp;
        $this->artwork = $artwork;
        $this->sprite = $sprite;
    }

    #[ArrayShape(["id" => "string", "name" => "string", "weight" => "int", "base_exp" => "int", "artwork" => "string", "sprite" => "string"])]
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "weight" => $this->weight,
            "base_exp" => $this->base_exp,
            "artwork" => $this->artwork,
            "sprite" => $this->sprite
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @return int
     */
    public function getBaseExp(): int
    {
        return $this->base_exp;
    }

    /**
     * @return string
     */
    public function getArtwork(): string
    {
        return $this->artwork;
    }

    /**
     * @return string
     */
    public function getSprite(): string
    {
        return $this->sprite;
    }


}