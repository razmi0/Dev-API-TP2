<?php

use DI\ContainerBuilder;

use DI\Container;

use Slim\{
    Factory\AppFactory,
    Handlers\Strategies\RequestResponseArgs,
};


const PROJECT_ROOT = __DIR__ . "/..";

require PROJECT_ROOT . '/vendor/autoload.php';

/**
 * Create a container using PHP-DI (Dependency Injection) builder
 * 
 * Slim 4 does not come with a DI container by default
 * I choose PHP-DI because it is a well known and documented DI container ( even in the Slim docs)
 * 
 * for dependency definitions : 
 * @see config/definitions.php
 */
$builder = new ContainerBuilder();


/**
 * @var Container $container
 */
$container = $builder
    ->addDefinitions(PROJECT_ROOT . '/config/definitions.php')
    ->build();



// We can now create the Slim App with the container configuration
AppFactory::setContainer($container);

$app = AppFactory::create();

// route collector seems to be a part of the Slim routing system
$collector = $app->getRouteCollector();

// Here we change the default behavior in regards to the way the route handler will parse the arguments from routes callbacks
$collector->setDefaultInvocationStrategy(new RequestResponseArgs());

// Decode Body middleware
$app->addBodyParsingMiddleware();

// Error Middleware ( 404 ect..) default to HTML but respoect Accept header from the request
$app->addErrorMiddleware(true, true, true);


// API
// --

require_once PROJECT_ROOT . '/config/routes.php';


$app->run();
