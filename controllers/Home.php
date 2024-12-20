<?php

namespace API\Controller;

use API\Model\Dao\UserDao;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class Home
{

    const RULES = [
        "username" => ["required", ["lengthBetween", 3, 50], ["regex", "/^[a-zA-Z0-9]+$/"]],
        "email" => ["required", "email"],
        "password" => ["required", ["lengthBetween", 8, 50], ["regex", "/^[a-zA-Z0-9]+$/"]],
        "confirm_password" => ["required", ["equals", "password"]]
    ];

    const BASIC_VIEW_DATA = [
        "title" => "Welcome",
    ];

    public function __construct(
        private PhpRenderer $view,
        private UserDao $userDao
    ) {}

    public function __invoke(Request $request, Response $response)
    {
        $viewData = isset($_SESSION["user_id"])
            ? [...self::BASIC_VIEW_DATA, "user" => $this->userDao->find("id", $_SESSION["user_id"])]
            : self::BASIC_VIEW_DATA;

        return $this->view->render($response, "home.php", $viewData);
    }
}
