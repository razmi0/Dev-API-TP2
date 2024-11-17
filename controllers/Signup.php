<?php

namespace API\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Valitron\Validator;

class Signup
{

    const RULES = [
        "username" => ["required", ["lengthBetween", 3, 50], ["regex", "/^[a-zA-Z0-9]+$/"]],
        "email" => ["required", "email"],
        "password" => ["required", ["lengthBetween", 8, 50], ["regex", "/^[a-zA-Z0-9]+$/"]],
        "confirm_password" => ["required", ["equals", "password"]]
    ];

    public function __construct(private PhpRenderer $view, private Validator $validator) {}

    public function new(Request $request, Response $response)
    {

        $viewData = [
            "title" => "Signup",
        ];

        return $this->view->render($response, "signup.php", $viewData);
    }

    public function create(Request $request, Response $response): Response
    {

        // data submited in form
        $data = $request->getParsedBody();

        // validation step
        $isValid = $this->runValidation(self::RULES, $data);

        if ($isValid === false) {

            // send the data and the errors to the view
            return $this->view->render($response, "signup.php", [
                "title" => "Signup",
                "errors" => $this->validator->errors(),
                "data" => $data
            ]);
        }

        // if the data is valid, we start the registration process

        // hasching password
        $data["password_hash"] = password_hash($data["password"], PASSWORD_DEFAULT);

        $api_key = bin2hex(random_bytes(32));

        $data["api_key"] = $api_key;

        print_r($data);

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
