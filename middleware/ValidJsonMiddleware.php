<?php

namespace API\Middleware;

use API\HTTP\Payload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


/**
 * 
 * class ValidJsonMiddleware
 * 
 * 
 * PSR-15 middleware that checks if the request body is a valid JSON
 * @see https://www.slimframework.com/docs/v4/concepts/middleware.html
 * 
 * @property ResponseFactoryInterface $responseFactory
 * 
 * @method __construct
 * @method process
 * 
 */
class ValidJsonMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * process
     * 
     * All PSR-15 middleware must implement this method
     * 
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        // Get the request body
        $json = $request->getBody()->getContents();

        // Check if the request body is not empty
        if (!empty($json)) {

            // Check if the request body is a valid JSON
            [$hasError, $decodedData, $error_message] = self::checkValidJson($json);

            // If the request body is not a valid JSON, return a 400 response with a body payload
            if ($hasError) {
                // Create a 400 response
                $response = $this->responseFactory->createResponse(400);

                // Create a body payload with infos about the error
                $payload = new Payload([
                    "message" => "Invalid JSON",
                    "data" => [],
                    "error" => $error_message
                ]);

                // Send the response
                return $response->getBody()->write($payload->toJson());
            }

            // If no error found, continue the process

            // Add the content type header 
            $request = $request->withHeader("Content-Type", "application/json");

            // and the parsed body to the request
            $request = $request->withParsedBody($decodedData);
        }

        // Continue for next step ( middleware or business logic )
        return $handler->handle($request);
    }

    /**
     * checkValidJson
     * 
     * @return array<bool, mixed, string>
     * 
     * */
    private static function checkValidJson($json): mixed
    {
        // Decode the JSON data
        $hasError = false;
        $decodedData = json_decode($json, true);
        $error_message = null;

        // Check for errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            $hasError = true;
            $error_message = json_last_error_msg();
        }

        // return the results as an array
        return [$hasError, $decodedData, $error_message];
    }
}
