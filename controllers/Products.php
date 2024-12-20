<?php

namespace API\Controller;

use API\Model\Dao\ProductDao;
use API\Model\Entity\Product;
use Psr\{
    Http\Message\ResponseInterface as Response,
    Http\Message\ServerRequestInterface as Request,
};

class Products
{


    public function __construct(

        /**
         * Create a new ProductDao object from DI container
         * ProductDao has Connection as a dependency
         * PHP DI autowiring will inject the Connection object as we defined it
         * @see ../config/definitions.php
         */

        private ProductDao $dao,
    ) {}


    /**
     * LIST
     */
    public function list(Request $request, Response $response)
    {
        $client_data = $request->getParsedBody();

        // Get the limit from the request if it exists in query params or body
        $limit =
            $request->getQueryParams()["limit"]
            ?? $client_data["limit"]
            ?? null;

        $limit = $limit !== null ? (int)$limit : null;

        // Get the offset from the request if it exists in query params or body
        $offset =
            $request->getQueryParams()["offset"]
            ?? $client_data["offset"]
            ?? 0;

        $offset = $offset !== null ? (int)$offset : 0;

        // Get the direction from the request if it exists in query params or body
        $direction =
            $request->getQueryParams()["direction"]
            ?? $client_data["direction"]
            ?? "DESC";

        $direction = $direction !== null ? $direction : "DESC";

        /**
         * Get all products from the database (with a limit if provided)
         * 
         * @var Product[] $allProducts
         */
        $allProducts = $this->dao->findAll($limit, $offset, $direction);


        // Map the products to an array
        $productsArray = Product::toArrayBulk($allProducts);

        $payload = json_encode(
            [
                "message" => "Produits trouvés",
                "data" => [
                    "products" => $productsArray
                ],
                "error" => ""
            ]
        );

        $response->getBody()->write($payload);

        return $response->withStatus(200);
    }



    /**
     * LISTONE
     */
    public function listOne(Request $request, Response $response, string $id)
    {

        // Get the product from the database
        $product = $this->dao->findById($id);

        // If the product is not found, we send a 404 with an error message
        if ($product === false) {

            $payload = json_encode(
                [
                    "message" => "Le produit n'a pas été trouvé.",
                    "data" => [],
                    "error" => "Not Found"
                ]
            );

            $response->getBody()->write($payload);

            return $response->withStatus(404);
        }


        $payload = json_encode(
            [
                "message" => "Produit trouvé",
                "data" => [
                    "product" => $product->toArray()
                ],
                "error" => ""
            ]
        );

        $response->getBody()->write($payload);

        return $response->withStatus(200);
    }




    /**
     * CREATE
     */
    public function create(Request $request, Response $response)
    {

        // Get the body of the request
        $client_data = $request->getParsedBody();

        // Create a new Product object
        $newProduct = Product::make($client_data);

        // The DAO create method create a new product in the database and return the inserted ID
        $insertedID = $this->dao->create($newProduct);

        if ($insertedID === null) {

            $payload = json_encode(
                [
                    "message" => "Une erreur s'est produite lors de la création du produit.",
                    "data" => [],
                    "error" => "Internal Server Error"
                ]
            );

            $response->getBody()->write($payload);

            return $response->withStatus(500);
        }


        $payload = json_encode(
            [
                "message" => "Produit créé",
                "data" => [
                    "id" => $insertedID
                ],
                "error" => ""
            ]
        );

        $response->getBody()->write($payload);

        return $response->withStatus(201);
    }




    /**
     * DELETE
     */
    public function delete(Request $request, Response $response)
    {


        /**
         * Get the id from the body
         * @var int $id
         */
        $id = (int)$request->getParsedBody()['id'] ?? null;

        // Get the product from the database
        $affectedRows = $this->dao->deleteById($id);

        // If no product was found, we send a 204 with no content in response body as HTTP specification states
        if ($affectedRows === false)
            return $response->withStatus(204);


        $payload = json_encode(
            [
                "message" => "Produit supprimé",
                "data" => [],
                "error" => ""
            ]
        );

        $response->getBody()->write($payload);

        return $response->withStatus(200);
    }





    /**
     * UPDATE
     */
    public function update(Request $request, Response $response)
    {

        // Get the client data
        /**
         * @var array Partial Product data
         */
        $client_data = $request->getParsedBody();

        // Create a new Product
        $product = Product::make($client_data);

        /**
         * Update the product in the database and store the number of affected rows
         */
        $affected_rows = $this->dao->update($product);


        if ($affected_rows === false)
            return $response->withStatus(204);


        $payload = json_encode(
            [
                "message" => "Produit mis à jour",
                "data" => [],
                "error" => ""
            ]
        );

        $response->getBody()->write($payload);

        return $response->withStatus(200);
    }
}
