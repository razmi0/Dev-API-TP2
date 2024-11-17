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

class Login
{

    const RULES = [
        "email" => ["required", "email"],
        "password" => ["required", ["lengthBetween", 8, 50], ["regex", "/^[a-zA-Z0-9]+$/"]]
    ];


    const BASIC_VIEW_DATA = [
        "title" => "Login",
        "errors" => [],
    ];

    public function __construct(
        private PhpRenderer $view,
        private UserDao $userDao,
        private Validator $validator
    ) {}

    public function new(Request $request, Response $response)
    {

        $viewData = [
            ...self::BASIC_VIEW_DATA
        ];

        return $this->view->render($response, "login.php", $viewData);
    }


    public function create(Request $request, Response $response): Response
    {

        // data submited in form
        $data = $request->getParsedBody();

        // validation step
        $isValid = $this->runValidation(self::RULES, $data);

        print_r($isValid);

        if ($isValid === false) {

            // send the data and the errors to the view
            return $this->view->render($response, "login.php", [

                ...self::BASIC_VIEW_DATA,

                "errors" => $this->validator->errors() ?? [],

                "data" => $data

            ]);
        }

        /**
         * @var User|false $user
         */
        $user = $this->userDao->find("email", $data["email"]);

        if (!$user || !password_verify($data["password"], $user->getPasswordHash())) {

            return $this->view->render($response, "login.php", [

                ...self::BASIC_VIEW_DATA,

                "errors" => ["global" => "Invalid email or password"],

                "data" => $data
            ]);
        }

        $_SESSION["user_id"] = $user->getId();

        return $response->withStatus(302)->withHeader("Location", "/");
    }


    public function destroy(Request $request, Response $response): Response
    {

        session_destroy();

        return $response->withStatus(302)->withHeader("Location", "/");
    }

    private function runValidation(array $rules, mixed $data): bool
    {

        $this->validator->mapFieldsRules($rules);

        $validator = $this->validator->withData($data);

        $this->validator = $validator;

        return $validator->validate();
    }
}
