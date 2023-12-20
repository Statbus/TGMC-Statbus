<?php

use App\Domain\User\Service\AuthenticateUser;
use App\Extension\Twig\EnumExtension;
use App\Extension\Twig\WebpackAssetLoader;
use App\Factory\LoggerFactory;
use App\Handler\DefaultErrorHandler;
use App\Service\AdminRankService;
use App\Service\ServerInformationService;
use Nyholm\Psr7\Factory\Psr17Factory;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\EasyDBCache;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\Factory\AppFactory;
use Slim\App;
use Slim\Interfaces\RouteParserInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;

return [
    // Application settings
    'settings' => fn () => require __DIR__ . '/settings.php',

    'request_id' => fn () => substr(strtoupper(bin2hex(random_bytes(32))), 0, 6),

    'servers' => fn (ContainerInterface $container) => ServerInformationService::getServerInfo($container->get('settings')['serverinfo']['url']),

    'rounds' => fn (ContainerInterface $container) => ServerInformationService::getCurrentRounds(
        $container->get('settings')['serverinfo']['game'],
        $container->get('servers')
    ),

    App::class => function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        // Register routes
        (require __DIR__ . '/routes.php')($app);

        // Register middleware
        (require __DIR__ . '/middleware.php')($app);

        return $app;
    },

    // HTTP factories
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    StreamFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UriFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    // The Slim RouterParser
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    // PDO::class => function (ContainerInterface $container) {
    //     $driver = $container->get(Connection::class)->getDriver();

    //     $class = new ReflectionClass($driver);
    //     $method = $class->getMethod('getPdo');
    //     $method->setAccessible(true);

    //     return $method->invoke($driver);
    // },

    // LoggerInterface::class => function (ContainerInterface $container) {
    //     $settings = $container->get('settings')['logger'];

    //     $logger = new Logger('app');

    //     if (isset($settings['path'])) {
    //         $filename = sprintf('%s/app.log', $settings['path']);
    //         $level = $settings['level'];
    //         $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level, true, 0777);
    //         $rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));
    //         $logger->pushHandler($rotatingFileHandler);
    //     }

    //     return $logger;
    // },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['error'];
        $app = $container->get(App::class);

        $logger = $container->get(LoggerFactory::class)
            ->addFileHandler('error.log')
            ->createLogger();


        $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details'],
            $logger
        );

        $errorMiddleware->setDefaultErrorHandler($container->get(DefaultErrorHandler::class));

        return $errorMiddleware;
    },

    //TwigMiddleware
    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer(
            $container->get(App::class),
            Twig::class
        );
    },

    //Twig
    Twig::class => function (ContainerInterface $container) {
        $session = $container->get(Session::class);
        $settings = $container->get("settings");
        $twigConfig = $settings['twig'];
        $appSettings = $settings['application'];

        $twigConfig['options']['cache'] = $twigConfig['options']['cache_enabled']
            ? $twigConfig['options']['cache_path']
            : false;

        $twig = Twig::create($twigConfig['paths'], $twigConfig['options']);

        $loader = $twig->getLoader();
        $publicPath = (string) $settings['public'];
        if ($loader instanceof FilesystemLoader) {
            $loader->addPath($publicPath, 'public');
        }

        $twig->getEnvironment()->addGlobal("debug", $settings["debug"]);
        $twig->getEnvironment()->addGlobal("app", $appSettings);
        $twig->getEnvironment()->addGlobal("flash", $session->getFlashBag()->all());
        $twig->getEnvironment()->addGlobal("user", $container->get(User::class));
        $twig->getEnvironment()->addGlobal('request_id', $container->get('request_id'));
        $twig->getEnvironment()->addGlobal('current_rounds', $container->get('rounds'));

        $twig->addExtension(new WebpackAssetLoader($settings['public'], $settings['debug']));
        $twig->addExtension(new EnumExtension());
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        $twig->addExtension(new StringExtension());

        // $twig->addRuntimeLoader(new class () implements RuntimeLoaderInterface {
        //     public function load($class)
        //     {
        //         $config = [
        //             'default_attributes' => [
        //                 Table::class => [
        //                     'class' => 'table table-bordered',
        //                 ],
        //             ],
        //         ];
        //         if (MarkdownRuntime::class === $class) {
        //             $environment = new Environment($config);
        //             $environment->addExtension(new CommonMarkCoreExtension());
        //             $environment->addExtension(new DefaultAttributesExtension());
        //             $environment->addExtension(new GithubFlavoredMarkdownExtension());
        //             return new MarkdownConverter($environment);
        //         }
        //     }
        // });
        // $twig->addExtension(new \Twig\Extra\Markdown\MarkdownExtension());
        $twig->getEnvironment()->getExtension(\Twig\Extension\CoreExtension::class)->setDateFormat(
            $appSettings['date_format'],
            $appSettings['interval_format']
        );
        $twig->getEnvironment()->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone($appSettings['timezone']);
        return $twig;
    },

    // 'view' => function (ContainerInterface $container) {
    //     return $container->get(Twig::class);
    // },

    //Session
    Session::class => function (ContainerInterface $container) {
        $settings = $container->get("settings")["session"];
        if (PHP_SAPI === "cli") {
            return new Session(new MockArraySessionStorage());
        } else {
            return new Session(new NativeSessionStorage($settings));
        }
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['database'];
        $dsn = sprintf(
            "mysql:host=%s:%s;dbname=%s",
            $settings['host'],
            $settings['port'],
            $settings['database']
        );
        $db = new \PDO(
            $dsn,
            $settings['username'],
            $settings['password'],
        );
        return $db;
    },

    EasyDB::class => function (ContainerInterface $container) {
        try {
            return EasyDBCache::fromEasyDB(new EasyDB($container->get(PDO::class)));
        } catch (Exception $e) {
            die("The TGMC database is not available. This should be a temporary error.");
        }
    },

    User::class => function (ContainerInterface $container) {
        return (new AuthenticateUser(
            $container,
            $container->get(EasyDB::class),
            $container->get(Session::class)
        ))->refreshUser();
    },

    AdminRankService::class => function () {
        return new AdminRankService();
    },

    LoggerFactory::class => function (ContainerInterface $container) {
        return new LoggerFactory($container->get('settings')['logger']);
    },


    // Application::class => function (ContainerInterface $container) {
    //     $application = new Application();

    //     $application->getDefinition()->addOption(
    //         new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev')
    //     );

    //     foreach ($container->get('settings')['commands'] as $class) {
    //         $application->add($container->get($class));
    //     }

    //     return $application;
    // },

    // ClockInterface::class => function () {
    //     return new NativeClock();
    // },
];
