<?php

namespace API\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Valitron\Validator;
use API\Model\{
    Entity\User,
    Dao\UserDao
};

class Signup
{

    const RULES = [
        "username" => ["required", ["lengthBetween", 3, 50], ["regex", "/^[a-zA-Z0-9]+$/"]],
        "email" => ["required", "email"],
        "password" => ["required", ["lengthBetween", 8, 50], ["regex", "/^[a-zA-Z0-9]+$/"]],
        "confirm_password" => ["required", ["equals", "password"]]
    ];

    const BASIC_VIEW_DATA = [
        "title" => "Signup",
    ];

    public function __construct(
        private PhpRenderer $view,
        private Validator $validator,
        private UserDao $userDao
    ) {}

    public function new(Request $request, Response $response)
    {

        $viewData = [
            ...self::BASIC_VIEW_DATA
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
                ...self::BASIC_VIEW_DATA,
                "errors" => $this->validator->errors(),
                "data" => $data
            ]);
        }

        // if the data is valid, we start the registration process

        // hashing password
        $data["password_hash"] = password_hash($data["password"], PASSWORD_DEFAULT);

        // generate an api key
        $api_key = bin2hex(random_bytes(32));

        // hashing the api key
        $data["api_key"] = $api_key;

        $data["api_key_hash"] = password_hash($api_key, PASSWORD_DEFAULT);

        // Creating user entity
        $user = User::make($data);

        // Creating user dao
        $insertedId = $this->userDao->create($user);

        print_r($insertedId);

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
