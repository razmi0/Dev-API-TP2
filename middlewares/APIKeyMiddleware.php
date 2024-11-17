<?php

namespace API\Middleware;

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

    public function __construct(private ResponseFactory $factory) {}


    public function __invoke(Request $request, Handler $handler): Response
    {
        // Proceed with the next middleware (Handles a request and produces a response.)
        $response = $handler->handle($request);

        $apikey = $request->hasHeader(self::HEADER_LABEL) ? $request->getHeaderLine(self::HEADER_LABEL) : null;

        if (empty($apikey)) {
            $response = $this->factory->createResponse(401);

            $payload = json_encode(
                [
                    "message" => "API Key is missing",
                    "error" => "Unauthorized",
                    "data" => []
                ]
            );

            $response->getBody()->write($payload);

            return $response;
        }

        return $response;
    }
}
