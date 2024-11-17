<?php

use API\Model\Dao\{Connection};
use Slim\Views\PhpRenderer;

/**
 * Dependency Injection configuration
 */
return [

    // ProductDao DI configuration ( connection data )
    Connection::class => function () {
        return new Connection(
            host: "localhost:3306",
            username: "root",
            password: "",
            db_name: "db_labrest",
            table_name: "T_PRODUIT"
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
