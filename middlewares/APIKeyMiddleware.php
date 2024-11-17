<?php

namespace API\Middleware;

use API\Model\Dao\UserDao;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Factory\ResponseFactory;


/**
 * HeaderMiddleware
 * 
 * Add headers to the response
 * 
 */
class APIKeyMiddleware
{

    const HEADER_LABEL = "X-API-KEY";

    public function __construct(private ResponseFactory $factory, private UserDao $userDao) {}


    public function __invoke(Request $request, Handler $handler): Response
    {
        // Proceed with the next middleware (Handles a request and produces a response.)
        $response = $handler->handle($request);

        $apikey = $request->getHeaderLine(self::HEADER_LABEL);

        if (empty($apikey)) {
            $response = $this->factory->createResponse(400);

            $payload = json_encode(
                [
                    "message" => "API Key is missing",
                    "error" => "Bad Request",
                    "data" => [
                        "apikey" => $apikey
                    ]
                ]
            );

            $response->getBody()->write($payload);

            return $response;
        }

        // we retrieve the api key hash from the api_key given by the user
        $api_key_hash = hash_hmac("sha256", $apikey, $_ENV["API_HASH_KEY"]);

        // we check if the api key hash exists in the database
        $user = $this->userDao->find("api_key_hash", $api_key_hash);

        // if the user is not found, we return an error
        if ($user === false) {
            $response = $this->factory->createResponse(401);

            $payload = json_encode(
                [
                    "message" => "API Key is missing",
                    "error" => "Unauthorized",
                    "data" => [
                        "apikey" => $apikey
                    ]
                ]
            );

            $response->getBody()->write($payload);

            return $response;
        }


        $payload = json_encode(
            [
                "message" => "API Key is valid",
                "error" => "",
                "data" => [
                    "user" => $user->toArray(),
                    "apikey" => $apikey
                ]
            ]
        );

        $response->getBody()->write($payload);

        return $response;
    }
}
