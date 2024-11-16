<?php

namespace API\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Valitron\Validator;
use Slim\Routing\RouteContext;
use Slim\Exception\HttpBadRequestException;



class BodyValidationMiddleware
{

    public const NAME_REGEX = "/^[a-zA-Z0-9-'%,.:\/&()|; ]+$/";
    public const DESCRIPTION_REGEX = "/^[a-zA-Z0-9-'%,.:\/&()|; ]+$/";

    const RULES = [
        'create' => [
            'name' => ['required', ['lengthBetween', 3, 50], ['regex', self::NAME_REGEX]],
            'description' => ['required', ['lengthBetween', 3, 255], ['regex', self::DESCRIPTION_REGEX]],
            'prix' => ['required', 'numeric', ['min', 0]]
        ],
        'list' => [
            'limit' => ['optional', 'integer', ['min', 1]],
            'offset' => ['optional', 'integer', ['min', 0]],
            'direction' => ['optional', ['in', ['ASC', 'DESC']]]
        ],
        'update' => [
            'id' => ['required', 'integer', ['min', 1]],
            'name' => ['optional', ['lengthBetween', 3, 50], ['regex', self::NAME_REGEX]],
            'description' => ['optional', ['lengthBetween', 3, 255], ['regex', self::DESCRIPTION_REGEX]],
            'prix' => ['required', 'numeric', ['min', 0]]
        ],
        'delete' => [
            'id' => ['required', 'integer', ['min', 1]]
        ]
    ];

    public function __construct(private Validator $validator) {}

    public function __invoke(Request $request, Handler $handler): Response
    {

        $response = $handler->handle($request);

        $currentController = RouteContext::fromRequest($request)->getRoute()->getCallable()[1];

        $rules = self::RULES[$currentController];

        $client_data = $request->getParsedBody();

        $isValid = $this->runValidation($rules, $client_data);

        if ($isValid === false) {

            $payload = json_encode(
                [
                    "message" => "Les données fournies ne sont pas valides",
                    "error" => "Bad Request",
                    "data" => $this->validator->errors()
                ]
            );

            throw new HttpBadRequestException($request, $payload);
        }

        return $response;
    }



    private function runValidation(array $rules, mixed $data): bool
    {

        $this->validator->mapFieldsRules($rules);

        $validator = $this->validator->withData($data);

        $this->validator = $validator;

        return $validator->validate();
    }
}
