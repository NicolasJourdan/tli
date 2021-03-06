<?php

namespace App\Entity;

class Keyword
{
    /** @var int */
    private $idK;

    /** @var string */
    private $name;

    public function __construct(int $idK, string $name)
    {
        $this->idK = $idK;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getIdK(): int
    {
        return $this->idK;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
