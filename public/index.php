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

// We add all headers to all responses
$app->add(HeaderMiddleware::class);

// API
// --

// Liste tous les produits
// query params: limit, offset, direction
$app->get("/api/v1.0/produit/list", [Products::class, "list"]);


// Liste un produit
$app->get("/api/v1.0/produit/listone/{id:[0-9]+}", [Products::class, "listOne"])
    ->add(IdMiddleware::class);


// Creer un produit
$app->post("/api/v1.0/produit/new", [Products::class, "create"]);


// Supprimer un produit
$app->delete("/api/v1.0/produit/delete", [Products::class, "delete"]);


// Update a product
$app->put("/api/v1.0/produit/update", [Products::class, "update"]);

$app->run();
