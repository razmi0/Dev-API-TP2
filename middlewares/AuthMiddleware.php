<?php

namespace API\Middleware;

use API\Model\Dao\UserDao;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Factory\ResponseFactory;


/**
 * AuthMiddleware
 * 
 */
class AuthMiddleware
{

    public function __construct(private ResponseFactory $factory, private UserDao $userDao) {}


    public function __invoke(Request $request, Handler $handler): Response
    {

        if (isset($_SESSION["user_id"])) {
            /**
             * @var User|false $user
             */
            $user = $this->userDao->find("id", $_SESSION["user_id"]);

            if ($user) {
                $request = $request->withAttribute("user", $user);
                return $handler->handle($request);
            }
        }

        $response = $this->factory->createResponse(401);

        $response->getBody()->write("Unauthorized");

        return $response;
    }
}
