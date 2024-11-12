<?php

namespace HTTP;

require_once "../../vendor/autoload.php";

use HTTP\Payload;

/**
 * Class Response
 * 
 * Used to create a response object
 * All API responses are sent using this class
 */
class Response
{
    private ?int $code = null;

    private array $header = [];

    // Default payload
    private ?Payload $payload = null;

    // The constructor sets a default configuration for the response
    // Code and methods are required
    public function __construct($config)
    {


        // valid config now
        $this->code = $config["code"];

        $this->payload = new Payload([
            "message" => $config["message"] ?? "",
            "data" =>  $config["data"] ?? [],
            "error" => $config["error"] ?? ""
        ]);

        // keep a default header if not provided
        $this->header = [
            "Access-Control-Allow-Methods: " => self::methodsToString($config["header"]["methods"]), // default ["GET"]
            "Content-Type: " => $config["header"]["content_type"] ?? "application/json", // default "application/json"
            "Access-Control-Allow-Origin: " => $config["header"]["origin"] ?? "*", // default "*"
            "Access-Control-Age: " => $config["header"]["age"] ?? 3600, // default 3600
            "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With" => ""
        ];
    }

    private static function methodsToString(array $methods): string
    {
        return implode(", ", $methods);
    }

    public function setCode($code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setMessage($message): self
    {
        $this->payload->setMessage($message);
        return $this;
    }

    public function setPayload($data): self
    {
        $this->payload->setData($data);
        return $this;
    }

    public function setError(string $error): self
    {
        $this->payload->setError($error);
        return $this;
    }

    public function setContentType(string $content_type): self
    {
        $this->header = [...$this->header, "content_type" => $content_type];
        return $this;
    }

    public function setOrigin(string $origin): self
    {
        $this->header = [...$this->header, "origin" => $origin];
        return $this;
    }

    public function setMethods(array $methods): self
    {
        $this->header = [...$this->header, "methods" => self::methodsToString($methods)];
        return $this;
    }

    public function setAge($age): self
    {
        $this->header = [...$this->header, "age" => $age];
        return $this;
    }

    public function sendAndDie()
    {

        // set the headers
        foreach ($this->header as $key => $value) {
            header($key . $value);
        }

        // set the response code
        http_response_code($this->code);

        // send the payload to the client
        echo $this->payload->toJson();

        // end the script

        die();
    }
}
