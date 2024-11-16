<?php

namespace API\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * HeaderMiddleware
 * 
 * Add headers to the response
 * 
 */
class HeaderMiddleware
{

    private $headers = [
        "Content-Type" => "application/json",
        "Access-Control-Allow-Origin" => "*",
        "Access-Control-Age" => 3600,
        "Access-Control-Allow-Headers" => "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"
    ];


    public function __invoke(Request $request, Handler $handler): Response
    {
        // Proceed with the next middleware (Handles a request and produces a response.)
        $response = $handler->handle($request);

        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }
}
