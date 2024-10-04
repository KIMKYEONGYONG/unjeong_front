<?php

namespace App\Core;

use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;

class EasyDatabase
{
    // Connect
    public function connect(string $dbName = 'DB_NAME'): EasyDB|null
    {
        $mysql_connect_str = "mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV[$dbName];
        return Factory::fromArray([
            $mysql_connect_str,
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        ]);
    }
}
