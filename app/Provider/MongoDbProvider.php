<?php

declare(strict_types=1);

namespace App\Provider;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

class MongoDbProvider
{
    private Database $database;

    public function __construct()
    {
        $this->database = (new Client($_ENV['MONGODB_SERVER'], [], ['root' => 'array', 'document' => 'array']))->selectDatabase('jiksanprime');
    }

    public function getCollection(string $collectionName) : Collection
    {
        return $this->database->selectCollection($collectionName);
    }


}