<?php

namespace API\Model\Dao;

use Exception;
use API\Model\{Entity\Product, Dao\Connection};
use API\HTTP\Error as Error;
use PDO;


/**
 * Class ProductDao
 * 
 * Used to interact with the database and the T_PRODUIT table
 * 
 * @property PDO $pdo
 * @property Connection $connection
 * 
 * @method create(Product $produit)
 * @method findAll(int $limit = null)
 * @method findById(int $id)
 * @method deleteById(int $id)
 * @method update(Product $produit)
 * @method findManyById(array $ids)
 * 
 */
class ProductDao
{
    private ?PDO $pdo = null;
    private ?Connection $connection = null;

    public function __construct()
    {

        $this->connection = new Connection();
        $this->pdo = $this->connection->getPDO();
    }

    /**
     * @param Product $produit
     * @throws Error 
     * @return string $insertedId
     */
    public function create($produit)
    {
        try {
            // Setup the error message

            // Build the query
            $query = "INSERT INTO T_PRODUIT (name, description, prix, date_creation)";
            $query .= " VALUES (:name, :description, :prix, :date_creation)";

            // Verify the preparation of the query
            $prepared = $this->pdo->prepare($query);
            if (!$prepared) {
                Error::HTTP500("Erreur interne");
            }

            // Bind the parameters
            $id = $produit->getId();
            $name = $produit->getProductName();
            $description = $produit->getDescription();
            $prix = $produit->getPrix();
            $date_creation = $produit->getDateCreation();

            if (!is_null($id))
                $prepared->bindParam(':id', $id, PDO::PARAM_INT);

            // No PARAM_* default to PARAM_STR
            if (!is_null($name))
                $prepared->bindParam(':name', $name);

            if (!is_null($description))
                $prepared->bindParam(':description', $description);

            // we use bindValue here because we want to keep the right type (double) for the price
            if (!is_null($prix))
                $prepared->bindValue(':prix', $prix);

            if (!is_null($date_creation))
                $prepared->bindParam(':date_creation', $date_creation);

            // Verify the execution of the query
            $stmt = $prepared->execute();
            if (!$stmt) {
                Error::HTTP500("Erreur interne");
            }

            // If all went good, we will return the id of the last inserted product in db to the controller
            return $this->pdo->lastInsertId();
        } catch (Error $e) {
            // If an error was catch, we send an informative error message back to the controller
            throw $e;
        }
        $this->connection->closeConnection();
    }


    /**
     * 
     * @description Find all products
     * @param int $limit
     * @throws Error
     * @return Product[]
     * 
     */
    public function findAll(int $limit = null): array
    {

        try {

            // Build the query
            $query = "SELECT * FROM " . $this->connection->getTableName() . " ORDER BY date_creation DESC";
            $query .= $limit ? " LIMIT :limit" : "";

            // Verify the preparation of the query
            $prepared = $this->pdo->prepare($query);
            if (!$prepared) {
                Error::HTTP500("Erreur interne");
            }

            // Bind the parameters
            if ($limit) {
                $prepared->bindParam(':limit', $limit, PDO::PARAM_INT);
            }

            // Verify the execution of the query
            $stmt = $prepared->execute();
            if (!$stmt) {
                Error::HTTP500("Erreur interne");
            }
            $products_from_db = $prepared->fetchAll();

            // If no product was found, we send a response with a 404 status code and an error message
            if (count($products_from_db) == 0) {
                Error::HTTP404("Aucun produit trouvé");
            }

            // We map the products from the database to a new array of products entities and return it to the controller
            return array_map(fn($product) => Product::make($product), $products_from_db);
        } catch (Error $e) {
            // If an error was catch, we send a response with a 500 status code and an error message
            throw $e;
        }
        $this->connection->closeConnection();
    }


    /**
     * 
     * @param int $id
     * @throws Error
     * @return Product
     * 
     */
    public function findById(int $id): Product
    {
        try {

            // Build the query
            $query = "SELECT * FROM " . $this->connection->getTableName() . " WHERE id = :id";

            // Verify the preparation of the query
            $prepared = $this->pdo->prepare($query);
            if (!$prepared) {
                Error::HTTP500("Erreur interne");
            }

            // Bind the parameters
            $prepared->bindParam(':id', $id, PDO::PARAM_INT);

            // Verify the execution of the query
            $stmt = $prepared->execute();
            if (!$stmt) {
                Error::HTTP500("Erreur interne");
            }

            // Fetch the result
            $result = $prepared->fetch();

            // If no product was found, we send a response with a 404 status code and an error message
            if (!$result) {
                Error::HTTP404("Aucun produit trouvé", ["id" => $id]);
            }

            // Create a new product object and return it to the controller
            return Product::make($result);
        } catch (Error $e) {
            // If an error was catch, we send a response with a 500 status code and an error message
            throw $e;
        }
        $this->connection->closeConnection();
    }

    /**
     * 
     * @description Delete a product by its id
     * @param $id
     * @throws Error
     * @return int
     * 
     */
    public function deleteById(int $id): int
    {

        try {
            // Build the query
            $query = "DELETE FROM " . $this->connection->getTableName() . " WHERE id = :id";
            // Verify the preparation of the query
            $prepared = $this->pdo->prepare($query);
            if (!$prepared) {
                Error::HTTP500("Erreur interne");
            }

            // Bind the parameters
            $prepared->bindParam(':id', $id, PDO::PARAM_INT);

            // Verify the execution of the query
            $stmt = $prepared->execute();
            if (!$stmt) {
                Error::HTTP500("Erreur interne");
            }

            // Affected rows in the database
            $affectedRows = $prepared->rowCount();

            // return the number of affected rows
            return $affectedRows;
        } catch (Error $e) {
            throw $e;
        }
        $this->connection->closeConnection();
    }


    /**
     * 
     * @param Product $produit
     * @throws Error
     * @return array
     * 
     */
    public function update(Product $produit): int
    {

        try {

            // Get the id of the product
            $id = $produit->getId();
            $name = $produit->getProductName();
            $description = $produit->getDescription();
            $prix = $produit->getPrix();

            // Build the query
            $query = "UPDATE " . $this->connection->getTableName() . " SET ";

            if (!empty($name))
                $query .= "name = :name, ";

            if (!empty($description))
                $query .= "description = :description, ";

            if (!empty($prix))
                $query .= "prix = :prix ";

            // Remove the last comma and space
            $query = rtrim($query, ", ");
            $query .= " WHERE id = :id";

            // Verify the preparation of the query
            $prepared = $this->pdo->prepare($query);
            if (!$prepared) {
                Error::HTTP500("Erreur interne", ["id" => $id]);
            }

            // Bind the parameters
            $prepared->bindParam(':id', $id, PDO::PARAM_INT);

            if (!empty($name))
                $prepared->bindParam(':name', $name);

            if (!empty($description))
                $prepared->bindParam(':description', $description);

            if (!empty($prix))
                $prepared->bindValue(':prix', $prix);

            // Verify the execution of the query
            $stmt = $prepared->execute();
            if (!$stmt) {
                Error::HTTP500("Erreur interne", ["id" => $id]);
            }

            $affectedRows = $prepared->rowCount();

            // return the number of affected rows
            return $affectedRows;
        } catch (Error $e) {
            // If an error was catch, we send an informative error message back to the controller
            throw $e;
        }
        $this->connection->closeConnection();
    }

    /**
     * 
     * @param array $ids
     * @throws Error
     * @return Product[]
     * 
     */
    public function findManyById(array $ids): array
    {
        try {

            // Build the query
            $query = "SELECT * FROM " . $this->connection->getTableName() . " WHERE id IN (";

            // We build the query with the number of ids
            $query .= implode(",", array_fill(0, count($ids), "?"));
            $query .= ")";

            // Verify the preparation of the query
            $prepared = $this->pdo->prepare($query);
            if (!$prepared) {
                Error::HTTP500("Erreur interne");
            }

            // Verify the execution of the query
            $stmt = $prepared->execute($ids);
            if (!$stmt) {
                Error::HTTP500("Erreur interne");
            }

            // Fetch the result
            $result = $prepared->fetchAll();

            // If no product was found, we send a response with a 404 status code and an error message
            if (!$result) {
                Error::HTTP404("Aucun produit trouvé", ["ids" => $ids]);
            }

            // We map the products from the database to a new array of products entities and return it to the controller
            return Product::makeBulk($result);
        } catch (Exception $e) {
            // If a not handled error was catch, we send a response with a 500 status code and an error message
            throw $e->getMessage();
        }
        $this->connection->closeConnection();
    }
}
