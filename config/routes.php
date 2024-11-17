<?php


use Slim\Routing\RouteCollectorProxy;
use API\Controller\{
    Home,
    Login,
    Products,
    Signup,
    Profile
};
use API\Middleware\{
    HeaderMiddleware,
    IdMiddleware,
    APIKeyMiddleware,
    AuthMiddleware,
    BodyValidationMiddleware,
    SessionMiddleware
};


/**
 * Public routes
 */

$app->group("", function (RouteCollectorProxy $publicRoutes) {

    $publicRoutes->get("/", Home::class);

    $publicRoutes->group("/signup", function (RouteCollectorProxy $signup) {

        $signup->get("", [Signup::class, "new"]);

        $signup->post("", [Signup::class, "create"]);

        $signup->get("/success", [Signup::class, "success"]);
    });


    $publicRoutes->group("/login", function (RouteCollectorProxy $login) {

        $login->get("", [Login::class, "new"]);

        $login->post("", [Login::class, "create"]);
    });

    $publicRoutes->group("", function (RouteCollectorProxy $authProtected) {

        $authProtected->get("/logout", [Login::class, "destroy"]);

        $authProtected->get("/profile", [Profile::class, "show"])
            ->add(AuthMiddleware::class);
    });

    //
})->add(SessionMiddleware::class);



/**
 * Protected by api key routes
 */
$app->group("/api/v1.0/produit/", function (RouteCollectorProxy $apiRoutes) {

    $apiRoutes->get("listone/{id:[0-9]+}", [Products::class, "listOne"])
        ->add(IdMiddleware::class);

    $apiRoutes->group("", function (RouteCollectorProxy $routeWithBodyValidation) {

        // list query params : limit, offset, order
        $routeWithBodyValidation->get("list", [Products::class, "list"]);

        $routeWithBodyValidation->post("new", [Products::class, "create"]);

        $routeWithBodyValidation->delete("delete", [Products::class, "delete"]);

        $routeWithBodyValidation->put("update", [Products::class, "update"]);
    })
        ->add(BodyValidationMiddleware::class);
})
    ->add(APIKeyMiddleware::class)
    ->add(HeaderMiddleware::class);
