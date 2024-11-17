<?php


use Slim\Routing\RouteCollectorProxy;
use API\Controller\{
    Products,
    Signup
};
use API\Middleware\{
    HeaderMiddleware,
    IdMiddleware,
    APIKeyMiddleware,
    BodyValidationMiddleware
};


$app->get("/signup", [Signup::class, "new"]);

$app->post("/signup", [Signup::class, "create"]);


$app->group("/api/v1.0/produit/", function (RouteCollectorProxy $group) {

    $group->get("listone/{id:[0-9]+}", [Products::class, "listOne"])
        ->add(IdMiddleware::class);

    $group->group("", function (RouteCollectorProxy $group) {

        // list query params : limit, offset, order
        $group->get("list", [Products::class, "list"]);

        $group->post("new", [Products::class, "create"]);

        $group->delete("delete", [Products::class, "delete"]);

        $group->put("update", [Products::class, "update"]);
    })
        ->add(BodyValidationMiddleware::class);
})
    ->add(APIKeyMiddleware::class)
    ->add(HeaderMiddleware::class);
