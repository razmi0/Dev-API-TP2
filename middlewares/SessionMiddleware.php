<?php

namespace API\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * SessionMiddleware
 * 
 * Add headers to the response
 * 
 */
class SessionMiddleware
{

    public function __invoke(Request $request, Handler $handler): Response
    {

        if (session_status() === PHP_SESSION_NONE) {

            session_start();
        }


        // Proceed with the next middleware (Handles a request and produces a response.)
        $response = $handler->handle($request);

        return $response;
    }
}
