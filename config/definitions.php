<?php

use API\Model\Dao\{Connection, ProductDao, UserDao};
use Slim\Views\PhpRenderer;

/**
 * Dependency Injection Container configuration
 */
return [

    // 
    /**
     * ProductDao DI configuration ( connection data )
     * @see API\Model\Dao\ProductDao
     * @see .env.local
     */
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


];
