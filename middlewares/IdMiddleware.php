<?php

namespace API\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Routing\RouteContext;

class IdMiddleware
{

    public function __invoke(Request $request, Handler $handler): Response
    {

        $response = $handler->handle($request);

        $context = RouteContext::fromRequest($request);

        $id = $context->getRoute()->getArguments("id");

        if ($id === null) {

            $id = $request->getParsedBody()["id"] ?? null;

            if ($id === null) {

                $payload = json_encode(
                    [
                        "message" => "Aucun id de produit n'a été fourni dans la requête.",
                        "data" => [],
                        "error" => "Bad Request"
                    ]
                );

                $response->getBody()->write($payload);

                return $response->withStatus(400);
            }
        }

        return $response;
    }
}
