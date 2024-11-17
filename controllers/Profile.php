<?php

namespace API\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use API\Model\{
    Entity\User,
    Dao\UserDao
};
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class Profile
{


    const BASIC_VIEW_DATA = [
        "title" => "Profile",
        "errors" => [],
    ];

    public function __construct(
        private PhpRenderer $view,
        private UserDao $userDao
    ) {}

    public function new(Request $request, Response $response)
    {

        $viewData = [
            ...self::BASIC_VIEW_DATA
        ];

        return $this->view->render($response, "profile.php", $viewData);
    }

    public function show(Request $request, Response $response): Response
    {
        $user = $request->getAttribute("user");

        $encryption_key = Key::loadFromAsciiSafeString($_ENV["API_ENCRYPTION_KEY"]);

        $api_key = Crypto::decrypt($user->getApiKey(), $encryption_key);

        $user->setApiKey($api_key);

        if ($user === null) {
            return $response->withHeader("Location", "/login")->withStatus(302);
        }

        return $this->view->render($response, "profile.php", [
            ...self::BASIC_VIEW_DATA,
            "user" => $user
        ]);
    }
}
