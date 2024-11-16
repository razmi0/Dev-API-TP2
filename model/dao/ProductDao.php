<?php

namespace API\Model\Dao;

use PDO;
use API\Model\{Entity\Product, Dao\Connection};


/**
 * Class ProductDao
 * 
 * Used to interact with a db and a table
 * 
 * @property PDO $pdo
 * @property Connection $connection
 * 
 * @method create(Product $produit)
 * @method findAll(int $limit = null, int offset = 0, string $direction = "DESC")
 * @method findById(int $id)
 * @method deleteById(int $id)
 * @method update(Product $produit)
 * @method findManyById(array $ids)
 * 
 */
class ProductDao
{
    private ?PDO $pdo = null;

    public function __construct(private Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $this->connection->getPDO();
    }

    /**
     * Create a new product in the database
     * @return int The id of the last inserted product in the database
     */
    public function create(Product $produit): int
    {
        // Build the query
        $query = "INSERT INTO " . $this->connection->getTableName() . " (name, description, prix, date_creation)";
        $query .= " VALUES (:name, :description, :prix, :date_creation)";

        // Verify the preparation of the query
        $prepared = $this->pdo->prepare($query);

        // Bind the parameters
        $id = $produit->getId();
        $name = $produit->getProductName();
        $description = $produit->getDescription();
        $prix = $produit->getPrix();
        $date_creation = $produit->getDateCreation();

        if (!is_null($id))
            $prepared->bindParam(':id', $id, PDO::PARAM_INT);

        if (!is_null($name))
            $prepared->bindParam(':name', $name);

        if (!is_null($description))
            $prepared->bindParam(':description', $description);

        if (!is_null($prix))
            $prepared->bindValue(':prix', $prix);

        if (!is_null($date_creation))
            $prepared->bindParam(':date_creation', $date_creation);

        // Execute the query
        $prepared->execute();

        $this->connection->closeConnection();

        // If all went good, we will return the id of the last inserted product in db to the controller
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Find all products and optionally order them by a column
     */
    public function findAll(int $limit = null, int $offset = 0, string $direction = "DESC"): array | false
    {
        // Build the query
        $query = "SELECT * FROM " . $this->connection->getTableName() . " ORDER BY date_creation " . $direction;

        // Add a limit to the query if it was provided
        $query .= $limit ? " LIMIT :limit" : "";

        // Add the offset to the query if it was provided
        $query .= $offset ? " OFFSET :offset" : "";

        // Verify the preparation of the query
        $prepared = $this->pdo->prepare($query);

        // Bind the limit if it was provided
        if ($limit) {
            $prepared->bindParam(':limit', $limit, PDO::PARAM_INT);
        }

        // Bind the offset if it was provided
        if ($offset) {
            $prepared->bindParam(':offset', $offset, PDO::PARAM_INT);
        }

        // Execute the query
        $prepared->execute();

        // Fetch the result
        $products_from_db = $prepared->fetchAll();

        $this->connection->closeConnection();

        // If no products were found, we return false
        if (empty($products_from_db)) {
            return false;
        }

        // We map the products from the database to a new array of products entities and return it to the controller
        return Product::makeBulk($products_from_db);
    }

    /**
     * Find a product by its id
     * @return Product | false False if no product was found
     */
    public function findById(int $id): Product | false
    {
        // Build the query
        $query = "SELECT * FROM " . $this->connection->getTableName() . " WHERE id = :id";

        // Verify the preparation of the query
        $prepared = $this->pdo->prepare($query);

        // Bind the parameters
        $prepared->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $prepared->execute();

        // Fetch the result
        $result = $prepared->fetch();

        $this->connection->closeConnection();

        if (empty($result)) {
            return false;
        }

        // Create a new product object and return it to the controller
        return Product::make($result);
    }

    /**
     * Delete a product by its id
     * @return int | false The number of affected rows or false if no row in the database was affected
     */
    public function deleteById(int $id): int | false
    {
        // Build the query
        $query = "DELETE FROM " . $this->connection->getTableName() . " WHERE id = :id";

        // Verify the preparation of the query
        $prepared = $this->pdo->prepare($query);

        // Bind the parameters
        $prepared->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $prepared->execute();

        // Affected rows in the database
        $affectedRows = $prepared->rowCount();

        $this->connection->closeConnection();

        if ($affectedRows === 0) {
            return false;
        }

        // return the number of affected rows
        return $affectedRows;
    }

    /**
     * Update a product in the database
     * Query is built dynamically based on the data provided
     * @return int | false The number of affected rows or false if no row in the database was affected
     */
    public function update(Product $produit): int | false
    {
        // Get the data from the product entity
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

        // Bind the parameters
        $prepared->bindParam(':id', $id, PDO::PARAM_INT);

        if (!empty($name))
            $prepared->bindParam(':name', $name);

        if (!empty($description))
            $prepared->bindParam(':description', $description);

        if (!empty($prix))
            $prepared->bindValue(':prix', $prix);

        // Execute the query
        $prepared->execute();

        // Affected rows in the database
        $affectedRows = $prepared->rowCount();

        $this->connection->closeConnection();

        if ($affectedRows === 0) {
            return false;
        }

        // return the number of affected rows
        return $affectedRows;
    }

    /**
     * @return Product[] | false  False if no products were found 
     * 
     */
    public function findManyById(array $ids): array | false
    {

        // Build the query
        $query = "SELECT * FROM " . $this->connection->getTableName() . " WHERE id IN (";

        // We build the query with the number of ids
        $query .= implode(",", array_fill(0, count($ids), "?"));
        $query .= ")";

        // Verify the preparation of the query
        $prepared = $this->pdo->prepare($query);

        // Execute the query
        $prepared->execute();

        // Fetch the result
        $result = $prepared->fetchAll();

        $this->connection->closeConnection();

        if (empty($result)) {
            return false;
        }

        // We map the products from the database to a new array of products entities and return it to the controller
        return Product::makeBulk($result);
    }
}
