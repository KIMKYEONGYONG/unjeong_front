<?php
/** @noinspection MissingService */

declare(strict_types=1);


use App\Core\Auth;
use App\Core\Config;
use App\Core\Csrf;
use App\Core\EntityManager\DefaultEntityManager;
use App\Core\EntityManager\EntityManagerFactory;
use App\Core\Session;
use App\DataObjects\SessionConfig;
use App\Enum\AppEnvironment;
use App\Enum\SameSite;
use App\Interfaces\AuthInterface;
use App\Interfaces\LoginProviderServiceInterface;
use App\Interfaces\RequestValidatorFactoryInterface;
use App\Interfaces\SessionInterface;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\LoginProviderService;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Twig\Extra\Html\HtmlExtension;
use Twig\Extra\Intl\IntlExtension;

use Twig\TwigFunction;

use function DI\create;

return [
    App::class => static function(ContainerInterface $container) : App {
        AppFactory::setContainer($container);

        $router    = require CONFIG_PATH . '/routes/route.php';
        $addMiddlewares = require CONFIG_PATH . '/middleware.php';

        $app = AppFactory::create();

        $router($app);
        $addMiddlewares($app);

        if (AppEnvironment::isProduction($container->get(Config::class)->get('app_environment'))) {
            $routeCollector = $app->getRouteCollector();
            $routeCollector->setCacheFile(STORAGE_PATH . '/cache/routes.cash');
        }

        return $app;
    },
    Config::class  => create(Config::class)->constructor(
        require CONFIG_PATH. '/settings.php'
    ),
    Twig::class => static function(Config $config, ContainerInterface $container) : Twig
    {
        $paths = [
            VIEW_PATH,
        ];
        $twig = Twig::create($paths,[
            'cache' => STORAGE_PATH . '/cache/templates',
            'auto_reload' => AppEnvironment::isDevelopment($config->get('app_environment')),
        ]);

        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new HtmlExtension());
        $twig->addExtension(new EntryFilesTwigExtension($container));
        $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));

        $twig->getEnvironment()->addFunction(new TwigFunction('page_link',function ($link,$args = []){
            $query = is_array($args)? http_build_query($args) : '';
            return !empty($link)? $link .  ($query ? '?'.$query : '') : '#';
        }));

        $twig->getEnvironment()->addFunction(new TwigFunction('list_no',function ($total,$page,$limit,$key){
            return $total - (($page - 1) * $limit) - $key;
        }));
        $twig->getEnvironment()->addFunction(new TwigFunction('urlencode',function ($url){
            return urlencode($url);
        }));

        return $twig;
    },
    DefaultEntityManager::class => static function(Config $config): EntityManager {
        return EntityManagerFactory::create(
            $config,'doctrine.connections.default',  DefaultEntityManager::class
        );
    },
    'webpack_encore.entrypoint_lookup_collection' => static function(): EntrypointLookupCollectionInterface  {
        $entrypointLookup = new EntrypointLookup(DIST_PATH . '/entrypoints.json');
        $serviceLocator =  new ServiceLocator(['_default' => function () use ($entrypointLookup) {
            return $entrypointLookup;
        }]);
        return new EntrypointLookupCollection($serviceLocator);
    },
    'webpack_encore.packages'               => static fn(): Packages => new Packages(
        new Package(new JsonManifestVersionStrategy(DIST_PATH . '/manifest.json'))
    ),
    'webpack_encore.tag_renderer' => static fn(ContainerInterface $container): TagRenderer => new TagRenderer(
        $container->get('webpack_encore.entrypoint_lookup_collection'),
        $container->get('webpack_encore.packages')
    ),
    ResponseFactoryInterface::class => static fn(App $app) => $app->getResponseFactory(),
    AuthInterface::class => static fn(ContainerInterface $container) => $container->get(
        Auth::class
    ),
    LoginProviderServiceInterface::class => static fn(ContainerInterface $container) => $container->get(
        LoginProviderService::class
    ),
    SessionInterface::class => static fn(Config $config) => new Session(
        new SessionConfig(
            $config->get('session.name', 'unjeong'),
            $config->get('session.flash_name', 'flash'),
            $config->get('session.secure', false),
            $config->get('session.httponly', true),
            SameSite::from($config->get('session.samesite', 'lax'))
        )
    ),
    RequestValidatorFactoryInterface::class => static fn(ContainerInterface $container) => $container->get(
        RequestValidatorFactory::class
    ),
    'csrf' => static fn(ResponseFactoryInterface $responseFactory, Csrf $csrf) => new Guard(
        $responseFactory, prefix: 'csrf_token',failureHandler: $csrf->failureHandler(),persistentTokenMode: true
    ),
];