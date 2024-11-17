<?php

namespace API\Model\Entity;

// COLUMNS : id, username, email, password, password_hash, api_key, api_key_hash, created_at, updated_at

/**
 * Class User
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $api_key
 * @property string $api_key_hash
 * @property string $created_at
 * @property string $updated_at
 * 
 * @method string getId()
 * @method string getUsername()
 * @method string getEmail()
 * @method string getPasswordHash()
 * @method string getApiKey()
 * @method string getApiKeyHash()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 * @method setId(string $id)
 * @method setUsername(string $username)
 * @method setEmail(string $email)
 * @method setPasswordHash(string $password_hash)
 * @method setApiKeyHash(string $api_key_hash)
 * @method setCreatedAt(string $created_at)
 * @method setUpdatedAt(string $updated_at)
 * @method toArray()
 */
class User
{
    public function __construct(
        private ?string $id = null,
        private ?string $username = null,
        private ?string $email = null,
        private ?string $password_hash = null,
        private ?string $api_key = null,
        private ?string $api_key_hash = null,
        private ?string $created_at = null,
        private ?string $updated_at = null
    ) {}

    public static function make(array $data): User
    {
        return new User(
            $data["id"] ?? null,
            $data["username"] ?? null,
            $data["email"] ?? null,
            $data["password_hash"] ?? null,
            $data["api_key"] ?? null,
            $data["api_key_hash"] ?? null,
            $data["created_at"] ?? date("Y-m-d H:i:s"),
            $data["updated_at"] ?? date("Y-m-d H:i:s")
        );
    }

    public static function makeBulk(array $data): array
    {
        $users = [];
        foreach ($data as $user) {
            $users[] = new User(
                $user["id"] ?? null,
                $user["username"] ?? null,
                $user["email"] ?? null,
                $user["password_hash"] ?? null,
                $user["api_key"] ?? null,
                $user["api_key_hash"] ?? null,
                $user["created_at"] ?? date("Y-m-d H:i:s"),
                $user["updated_at"] ?? date("Y-m-d H:i:s")
            );
        }
        return $users;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    public function setPasswordHash($password_hash)
    {
        $this->password_hash = $password_hash;
        return $this;
    }

    public function getApiKey()
    {
        return $this->api_key;
    }

    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
    }

    public function getApiKeyHash()
    {
        return $this->api_key_hash;
    }

    public function setApiKeyHash($api_key_hash)
    {
        $this->api_key_hash = $api_key_hash;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "username" => $this->username,
            "email" => $this->email,
            "password_hash" => $this->password_hash,
            "api_key" => $this->api_key,
            "api_key_hash" => $this->api_key_hash,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public static function toArrayBulk(array $users): array
    {
        return array_map(fn($user) => $user->toArray(), $users);
    }
}
