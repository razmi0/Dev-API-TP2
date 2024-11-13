<?php

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use DI\Container;
use Psr\{
    Http\Message\ResponseInterface as Response,
    Http\Message\ServerRequestInterface as Request,
    Container\ContainerInterface,
    Http\Message\ResponseFactoryInterface,
};
use API\{
    Middleware\Validators\Constant,
    Middleware\Validators\Validator,
    Middleware\ValidJsonMiddleware,
    Model\Dao\ProductDao,
    Model\Entity\Product,
    HTTP\Payload,
    Middleware\ValidatorMiddleware
};


require __DIR__ . '/../vendor/autoload.php';


// Create Container using PHP-DI 
// Slim 4 doesn't come with a DI container by default => composer require php-di/php-di
$container = new Container();


// Set the ResponseFactoryInterface in the container ( we use it to create a Slim Response inside middlewares)
$container->set(ResponseFactoryInterface::class, function (ContainerInterface $container) {
    return $container->get(ResponseFactory::class);
});


// Set the ValidJsonMiddleware in the container with the ResponseFactoryInterface dependency
$container->set(ValidJsonMiddleware::class, function (ContainerInterface $container) {
    return new ValidJsonMiddleware($container->get(ResponseFactoryInterface::class));
});

// CREATE VALIDATOR MIDDLEWARE DI CONFIGURATION
$container->set("CREATE" . ValidatorMiddleware::class, function (ContainerInterface $container) {
    return new ValidatorMiddleware($container->get(ResponseFactoryInterface::class), new Validator(
        [
            "name" => [
                "type" => "string",
                "range" => [1, 65],
                "regex" => Constant::NAME_REGEX,
                "required" => true,
                "nullable" => false
            ],
            "description" => [
                "type" => "string",
                "range" => [1, 65000],
                "regex" => Constant::DESCRIPTION_REGEX,
                "required" => true,
                "nullable" => false
            ],
            "prix" => [
                "type" => "double",
                "range" => [0, null],
                "regex" => Constant::PRICE_REGEX,
                "required" => true,
                "nullable" => false
            ]
        ]
    ));
});


// UPDATE VALIDATOR MIDDLEWARE DI CONFIGURATION
$container->set("UPDATE" . ValidatorMiddleware::class, function (ContainerInterface $container) {
    return new ValidatorMiddleware(
        $container->get(ResponseFactoryInterface::class),
        new Validator(
            [
                "id" => [
                    "type" => "integer",
                    "required" => true,
                    "range" => [1, null],
                    "regex" => Constant::ID_REGEX
                ],
                "name" => [
                    "type" => "string",
                    "range" => [1, 65],
                    "regex" => Constant::NAME_REGEX,
                    "required" => false,
                    "nullable" => true
                ],
                "description" => [
                    "type" => "string",
                    "range" => [1, 65000],
                    "regex" => Constant::DESCRIPTION_REGEX,
                    "required" => false,
                    "nullable" => true
                ],
                "prix" => [
                    "type" => "double",
                    "range" => [0, null],
                    "regex" => Constant::PRICE_REGEX,
                    "required" => false,
                    "nullable" => true
                ]
            ]
        )
    );
});

// DELETE VALIDATOR MIDDLEWARE DI CONFIGURATION
$container->set("DELETE" . ValidatorMiddleware::class, function (ContainerInterface $container) {
    return new ValidatorMiddleware(
        $container->get(ResponseFactoryInterface::class),
        new Validator(
            [
                "id" => [
                    "type" => "integer",
                    "required" => true,
                    "range" => [1, null],
                    "regex" => Constant::ID_REGEX
                ]
            ]
        )
    );
});


// We can now create the Slim App with the container configuration
$app = AppFactory::createFromContainer($container);

/**
 * 
 * [todo]
 *      (1) refactor and inject response slim dependency
 *      (2) refactor response to use slim response too
 *      (4) mettre des header aussi quand erreur
 *      (3) refactor middlewares
 * 
 */


$headers = [
    "Content-Type" => "application/json",
    "Access-Control-Allow-Origin" => "*",
    "Access-Control-Age" => 3600,
    "Access-Control-Allow-Headers" => "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"
];


// API
// --

// Liste de produit tout les produits
$app->get("/api/v1.0/produit/list", function (Request $request, Response $response) use ($headers) {

    $ENDPOINT_METHOD = "GET";

    // Get the limit from the request if it exists in query params
    $limit = null;
    if (array_key_exists('limit', $request->getQueryParams())) {
        $limit = (int)$request->getQueryParams()['limit'];
    }

    // Slim 4 does not parse the body of a GET request so no limit param in body

    /**
     * Create a new ProductDao object
     * @see model/dao/ProductDao.php
     */
    $dao = new ProductDao();

    // Get all products from the database (with a limit if provided)
    /**
     * @var Product[] $allProducts
     */
    $allProducts = $dao->findAll($limit);

    // Set the headers for the response
    $headers = [...$headers, "Access-Control-Allow-Methods" => $ENDPOINT_METHOD];
    foreach ($headers as $key => $value)
        $response = $response->withHeader($key, $value);


    // Map the products to an array
    $productsArray = array_map(fn($product) => $product->toArray(), $allProducts);

    $payload = new Payload([
        "message" => "Produits trouvés",
        "data" => [
            "products" => $productsArray
        ],
        "error" => ""
    ]);

    $response = $response->withStatus(200);
    $response->getBody()->write($payload->toJson());

    return $response;
});

// Liste un produit
$app
    ->get("/api/v1.0/produit/listone/{id}", function (Request $request, Response $response, $args) use ($headers) {

        $ENDPOINT_METHOD = "GET";

        $id = array_key_exists('id', $args) ? (int)$args['id'] : null;
        // If the id is not present in the query or in the body, throw an error
        if ($id === null) {
            $response = $response->withStatus(400);
            $payload = new Payload([
                "message" => "Aucun id de produit n'a été fourni dans la requête.",
                "data" => [],
                "error" => "Bad Request"
            ]);
            $response->getBody()->write($payload->toJson());
            return $response;
        }
        // Start the DAO
        $dao = new ProductDao();

        // Get the product from the database
        $product = $dao->findById($id);

        // If the product is not found, throw an error
        if ($product === null) {
            $response = $response->withStatus(404);
            $payload = new Payload([
                "message" => "Le produit n'a pas été trouvé.",
                "data" => [],
                "error" => "Not Found"
            ]);
            $response->getBody()->write($payload->toJson());
            return $response;
        }

        // Set the headers for the response
        $headers = [...$headers, "Access-Control-Allow-Methods" => $ENDPOINT_METHOD];

        foreach ($headers as $key => $value)
            $response = $response->withHeader($key, $value);

        $payload = new Payload([
            "message" => "Produit trouvé",
            "data" => [
                "product" => $product->toArray()
            ],
            "error" => ""
        ]);

        $response = $response->withStatus(200);
        $response->getBody()->write($payload->toJson());

        return $response;
    });


// Creer un produit
$app
    ->post("/api/v1.0/produit/new", function (Request $request, Response $response) use ($headers) {
        $ENDPOINT_METHOD = "POST";

        // Get the body of the request
        $client_data = $request->getParsedBody();

        // If the body is empty, throw an error
        if (empty($client_data)) {
            $response = $response->withStatus(400);
            $payload = new Payload([
                "message" => "Aucune donnée n'a été fournie dans la requête.",
                "data" => [],
                "error" => "Bad Request"
            ]);
            $response->getBody()->write($payload->toJson());
            return $response;
        }

        // Create a new Product object
        $newProduct = Product::make($client_data);

        // Start the DAO
        $dao = new ProductDao();

        // The DAO create method create a new product in the database and return the inserted ID
        $insertedID = $dao->create($newProduct);

        if ($insertedID === null) {
            $response = $response->withStatus(500);
            $payload = new Payload([
                "message" => "Une erreur s'est produite lors de la création du produit.",
                "data" => [],
                "error" => "Internal Server Error"
            ]);
            $response->getBody()->write($payload->toJson());
            return $response;
        }

        // Set the headers for the response
        $headers = [...$headers, "Access-Control-Allow-Methods" => $ENDPOINT_METHOD];

        foreach ($headers as $key => $value)
            $response = $response->withHeader($key, $value);

        $payload = new Payload([
            "message" => "Produit créé",
            "data" => [
                "id" => $insertedID
            ],
            "error" => ""
        ]);

        $response = $response->withStatus(201);
        $response->getBody()->write($payload->toJson());

        return $response;
    })
    ->add("CREATE" . ValidatorMiddleware::class)
    ->add(ValidJsonMiddleware::class);

// Supprimer un produit
$app
    ->delete("/api/v1.0/produit/delete", function (Request $request, Response $response) use ($headers) {

        $ENDPOINT_METHOD = "DELETE";

        /**
         * Get the id from the body
         * @var int $id
         */
        $id = (int)$request->getParsedBody()['id'] ?? null;


        if ($id === null) {
            $response = $response->withStatus(400);
            $payload = new Payload([
                "message" => "Aucun id de produit n'a été fourni dans le body de la requête.",
                "data" => [],
                "error" => "Bad Request"
            ]);
            $response->getBody()->write($payload->toJson());
            return $response;
        }

        // Start the DAO
        $dao = new ProductDao();

        // Get the product from the database
        $affectedRows = $dao->deleteById($id);

        // If no product was found, we send a 204 with no content in response body as HTTP specification states
        if ($affectedRows === 0) {
            $response = $response->withStatus(204);
            return $response;
        }

        // Set the headers for the response
        $headers = [...$headers, "Access-Control-Allow-Methods" => $ENDPOINT_METHOD];


        foreach ($headers as $key => $value)
            $response = $response->withHeader($key, $value);

        $payload = new Payload([
            "message" => "Produit supprimé",
            "data" => [],
            "error" => ""
        ]);

        $response = $response->withStatus(200);
        $response->getBody()->write($payload->toJson());

        return $response;
    })
    ->add("DELETE" . ValidatorMiddleware::class)
    ->add(ValidJsonMiddleware::class);

// Update a product
$app
    ->put("/api/v1.0/produit/update", function (Request $request, Response $response) use ($headers) {

        $ENDPOINT_METHOD = "PUT";

        // Get the client data
        /**
         * @var array Partial Product data
         */
        $client_data = $request->getParsedBody();

        var_export($client_data);


        // Create a new Product
        $product = Product::make($client_data);

        // Start the DAO
        $dao = new ProductDao();


        /**
         * Update the product in the database and store the number of affected rows
         * @var int $affected_rows
         */
        $affected_rows = $dao->update($product);

        $headers = [...$headers, "Access-Control-Allow-Methods" => $ENDPOINT_METHOD];

        foreach ($headers as $key => $value)
            $response = $response->withHeader($key, $value);


        if ($affected_rows === 0) {
            $response = $response->withStatus(204);
            return $response;
        }


        return $response;
    })
    ->add("UPDATE" . ValidatorMiddleware::class)
    ->add(ValidJsonMiddleware::class);


$app->run();
