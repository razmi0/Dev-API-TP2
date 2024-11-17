<?php

namespace API\Model\Dao;

use PDO;
use API\Model\{Entity\User, Dao\Connection};

// COLUMNS : id, username, email, password_hash, api_key_hash, created_at, updated_at

/**
 * Class UserDao
 * 
 * Used to interact with a db and a table
 * 
 * @property PDO $pdo
 * @property Connection $connection
 * 
 * @method create(User $user)
 * 
 */
class UserDao
{
    private ?PDO $pdo = null;

    public function __construct(private Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $this->connection->getPDO();
    }

    /**
     * Create a new user in the database
     * @return int The id of the last inserted user in the database
     */
    public function create(User $user): int
    {
        // Build the query
        $query = "INSERT INTO " . $this->connection->getTableName() . " (username, email, password_hash, api_key_hash)";
        $query .= " VALUES (:username, :email, :password_hash, :api_key_hash)";


        var_dump($this->connection->getTableName());


        // Verify the preparation of the query
        $prepared = $this->pdo->prepare($query);

        // Bind the parameters
        $username = $user->getUsername();
        $email = $user->getEmail();
        $password_hash = $user->getPasswordHash();
        $api_key_hash = $user->getApiKeyHash();

        if (!is_null($username))
            $prepared->bindParam(':username', $username);

        if (!is_null($email))
            $prepared->bindParam(':email', $email);

        if (!is_null($password_hash))
            $prepared->bindParam(':password_hash', $password_hash);

        if (!is_null($api_key_hash))
            $prepared->bindParam(':api_key_hash', $api_key_hash);


        // Execute the query
        $prepared->execute();

        $this->connection->closeConnection();

        // If all went good, we will return the id of the last inserted product in db to the controller
        return (int)$this->pdo->lastInsertId();
    }



    public function find(string $columns, mixed $value): User | bool
    {
        $query = "SELECT * FROM " . $this->connection->getTableName() . " WHERE $columns = :value";

        $prepared = $this->pdo->prepare($query);

        $prepared->bindParam(':value', $value);

        $prepared->execute();

        $fetch_result = $prepared->fetch();

        if ($fetch_result)
            return User::make($fetch_result);

        return $fetch_result;
    }
}
