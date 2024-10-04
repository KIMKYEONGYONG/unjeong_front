<?php

declare(strict_types=1);

namespace App\Core\EntityManager;

use App\Core\Config;
use App\Enum\AppEnvironment;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

class EntityManagerFactory
{
    /**
     * @throws Exception
     */
    public static function create(Config $c, string $connectionKey,$entityManagerClass = EntityManager::class): EntityManager {
        if (!is_subclass_of($entityManagerClass, EntityManager::class)) {
            throw new InvalidArgumentException("$entityManagerClass should be a subclass of EntityManager.");
        }

        $queryCache = AppEnvironment::isProduction($c->get('app_environment'))
            ? new PhpFilesAdapter('doctrine_queries')
            : new ArrayAdapter();

        $metadataCache = AppEnvironment::isProduction($c->get('app_environment'))
            ? new PhpFilesAdapter('doctrine_metadata')
            : new ArrayAdapter();

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $c->get('doctrine.entity_dir'),
            isDevMode: $c->get('doctrine.dev_mode'),
            proxyDir: $c->get('doctrine.proxy_dir')
        );
        $config->setProxyNamespace('ValueWalkApp\Proxies');
        $config->setMetadataCache($metadataCache);
        $config->setQueryCache($queryCache);

        $connection = DriverManager::getConnection(
            $c->get($connectionKey),
            $config
        );

        return new $entityManagerClass($connection, $config);
    }

}