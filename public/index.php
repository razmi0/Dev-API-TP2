<?php

use HTTP\Payload;
use Model\Dao\ProductDao;
use Model\Entity\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;


/**
 * 
 * [todo]
 *      (1) refactor and inject response slim dependency
 *      (2) refactor response to use slim response too
 *      (4) mettre des header aussi quand erreur
 *      (3) refactor middlewares
 * 
 */

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$headers = [
    "Content-Type" => "application/json",
    "Access-Control-Allow-Origin" => "*",
    "Access-Control-Age" => 3600,
    "Access-Control-Allow-Headers" => "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"
];

$app->addBodyParsingMiddleware();

// // API
// // Liste de produit tout les produits
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

// // Liste un produit
$app->get("/api/v1.0/produit/listone/{id}", function (Request $request, Response $response, $args) use ($headers) {

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
$app->post("/api/v1.0/produit/new", function (Request $request, Response $response) use ($headers) {
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
});

// Supprimer un produit
$app->delete("/api/v1.0/produit/delete", function (Request $request, Response $response) use ($headers) {

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
});

// Update a product
$app->put("/api/v1.0/produit/update", function (Request $request, Response $response) use ($headers) {

    $ENPOINT_METHOD = "PUT";

    // Get the client data
    /**
     * @var array Partial Product data
     */
    $client_data = $request->getParsedBody();


    // Create a new Product
    $product = Product::make($client_data);

    // Start the DAO
    $dao = new ProductDao();


    /**
     * Update the product in the database and store the number of affected rows
     * @var int $affected_rows
     */
    $affected_rows = $dao->update($product);


    if ($affected_rows === 0) {
        $response = $response->withStatus(204);
        return $response;
    }


    return $response;
});

$app->run();
