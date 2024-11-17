<?php

use API\Model\Dao\{Connection, ProductDao, UserDao};
use Slim\Views\PhpRenderer;

/**
 * Dependency Injection configuration
 */
return [

    // ProductDao DI configuration ( connection data )
    ProductDao::class => function () {
        return new ProductDao(
            new Connection(
                host: $_ENV["DB_HOST"],
                username: $_ENV["DB_USER"],
                password: $_ENV["DB_PASS"],
                db_name: $_ENV["DB_NAME"],
                table_name: $_ENV["DB_TABLE_PRODUCTS"]
            )
        );
    },


    // UserDao DI configuration ( connection data )
    UserDao::class => function () {
        return new UserDao(
            new Connection(
                host: $_ENV["DB_HOST"],
                username: $_ENV["DB_USER"],
                password: $_ENV["DB_PASS"],
                db_name: $_ENV["DB_NAME"],
                table_name: $_ENV["DB_TABLE_USERS"]
            )
        );
    },

    // PhpRenderer DI configuration
    PhpRenderer::class => function () {
        // Create a new PhpRenderer instance with the views directory
        $renderer = new PhpRenderer(__DIR__ . "/../views");

        // Set the layout file
        $renderer->setLayout("layout.php");

        return $renderer;
    }


];
