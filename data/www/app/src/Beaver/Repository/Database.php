<?php

namespace Beaver\Repository;

class Database extends \PDO
{
    public function __construct(string $dsn, string $username, string $password)
    {
        parent::__construct($dsn, $username, $password);
    }
}
