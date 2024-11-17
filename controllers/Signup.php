<?php

namespace API\Controller;

use Slim\Views\PhpRenderer;
use Valitron\Validator;
use Psr\{
    Http\Message\ResponseInterface as Response,
    Http\Message\ServerRequestInterface as Request
};
use API\Model\{
    Entity\User,
    Dao\UserDao
};
use Defuse\{
    Crypto\Crypto,
    Crypto\Key
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
        "errors" => [],
    ];

    public function __construct(
        private PhpRenderer $view,
        private Validator $validator,
        private UserDao $userDao
    ) {
        // We add a custom validation rule to check if the email is already in the database
        $this->validator->rule(function ($field, $value, $params, $fields) {

            return  $this->userDao->find("email", $value) === false;
        }, "email")->message("{field} already exists");
    }

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
                "errors" => $this->validator->errors() ?? [],
                "data" => $data
            ]);
        }


        // if the data is valid, we start the registration process

        // hashing password
        $data["password_hash"] = password_hash($data["password"], PASSWORD_DEFAULT);

        // generate an api key
        $api_key = bin2hex(random_bytes(32));

        // get the encryption key
        /**
         * @var Key
         */
        $encryption_key = Key::loadFromAsciiSafeString($_ENV["API_ENCRYPTION_KEY"]);

        $data["api_key"] = Crypto::encrypt($api_key, $encryption_key);

        // hashing the api key
        $data["api_key_hash"] = hash_hmac("sha256", $api_key, $_ENV["API_HASH_KEY"]);

        // Creating user entity
        $user = User::make($data);

        // Creating user dao
        $this->userDao->create($user);

        return $response->withStatus(302)->withHeader("Location", "/signup/success");
    }

    public function success(Request $request, Response $response): Response
    {
        return $this->view->render($response, "signup-success.php", self::BASIC_VIEW_DATA);
    }

    private function runValidation(array $rules, mixed $data): bool
    {

        $this->validator->mapFieldsRules($rules);

        $validator = $this->validator->withData($data);

        $this->validator = $validator;

        return $validator->validate();
    }
}
