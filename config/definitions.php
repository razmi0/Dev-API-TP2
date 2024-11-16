<?php

use API\Middleware\HeaderMiddleware;
use API\Model\Dao\{Connection, ProductDao};

use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface,
};
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteCollector;
use Valitron\Validator;

/**
 * Dependency Injection configuration
 */
return [

    // ProductDao DI configuration
    Connection::class => function () {
        return new Connection(
            host: "localhost:3306",
            username: "root",
            password: "",
            db_name: "db_labrest",
            table_name: "T_PRODUIT"
        );
    },


];
