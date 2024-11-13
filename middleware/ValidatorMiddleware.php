<?php

namespace API\Middleware;

use API\HTTP\Payload;
use API\Middleware\Validators\Validator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidatorMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;
    private Validator $validator;


    public function __construct(ResponseFactoryInterface $responseFactory, Validator $validator)
    {
        $this->responseFactory = $responseFactory;
        $this->validator = $validator;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        // Proceed with the next middleware (Handles a request and produces a response.)
        $response = $handler->handle($request);

        $decodedData = $request->getParsedBody();

        [$isValid, $errors] = $this->checkExpectedData($decodedData);


        if (!$isValid) {

            $newResponse = $this->responseFactory->createResponse(400);

            $newResponse = $newResponse->withHeader("Content-Type", "application/json");

            $payload = new Payload([
                "message" => "Bad Request",
                "data" => $errors,
                "error" => "Unexpected data"
            ]);

            $newResponse->getBody()->write($payload->toJson());

            return $newResponse;
        }

        return $response;
    }




    /**
     * Middleware
     * 
     * @param array $decoded_data
     * @return array<bool, array>
     * 
     * */
    public function checkExpectedData(array $decoded_data): array
    {
        // We check if a schema is defined
        // If a schema is defined, we parse the client data with the schema
        // If the client data is invalid against the schema, we return an error
        // else we return true
        $isValid = true;
        $errors = [];
        if ($this->validator) {
            $isValid = $this->validator->safeParse($decoded_data)->getIsValid();
            if (!$isValid) {
                $errors = $this->validator->getErrors();
            }
        }
        return [$isValid, $errors];
    }
}
