<?php

use DI\ContainerBuilder;

use DI\Container;

use Slim\{
    Factory\AppFactory,
    Handlers\Strategies\RequestResponseArgs,
    Routing\RouteCollectorProxy
};

use API\{
    Middleware\HeaderMiddleware,
    Controller\Products,
    Middleware\IdMiddleware,
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

// Error Middleware
$error_middleware = $app->addErrorMiddleware(true, true, true);

// Get the default error handler
$error_handler = $error_middleware->getDefaultErrorHandler();

// Force the content type to be JSON for all errors
$error_handler->forceContentType('application/json');






// API
// --




$app->group("/api/v1.0/produit/", function (RouteCollectorProxy $group) {

    // list query params : 
    // - limit : number of products to return
    // - offset : number of products to skip
    // - order : order by a column
    $group->get("list", [Products::class, "list"]);

    $group->get("listone/{id:[0-9]+}", [Products::class, "listOne"])
        ->add(IdMiddleware::class);

    $group->post("new", [Products::class, "create"]);

    $group->delete("delete", [Products::class, "delete"]);

    $group->put("update", [Products::class, "update"]);
})
    // We add all headers to all responses
    ->add(HeaderMiddleware::class);


$app->run();
