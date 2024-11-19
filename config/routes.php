<?php


use Slim\Routing\RouteCollectorProxy;
use API\Controller\{
    Products,
};
use API\Middleware\{
    HeaderMiddleware,
    IdMiddleware,
    BodyValidationMiddleware,
};





/**
 * Public API routes
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
    ->add(HeaderMiddleware::class);
