<?php

namespace HTTP;

require_once "vendor/autoload.php";

/**
 * Class Payload
 * 
 * Used to create a payload object (message, data, error)
 * All API responses and Errors send this payload in the response body
 */
class Payload
{
    private string $message = "";
    private array $data = [];
    private string $error = "";

    public function __construct(array $config)
    {
        $this->message = $config["message"] ?? "";
        $this->data = $config["data"] ?? [];
        $this->error = $config["error"] ?? "";
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function toJson()
    {
        return json_encode([
            "message" => $this->message,
            "data" => $this->data,
            "error" => $this->error
        ]);
    }

    public function toArray()
    {
        return [
            "message" => $this->message,
            "data" => $this->data,
            "error" => $this->error
        ];
    }
}
