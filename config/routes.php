<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\UserBundle\Controller\UserController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('siganushka_user_getcollection', '/users')
        ->controller([UserController::class, 'getCollection'])
        ->methods(['GET'])
        ->stateless(true)
    ;

    $routes->add('siganushka_user_postcollection', '/users')
        ->controller([UserController::class, 'postCollection'])
        ->methods(['POST'])
        ->stateless(true)
    ;

    $routes->add('siganushka_user_getitem', '/users/{id<\d+>}')
        ->controller([UserController::class, 'getItem'])
        ->methods(['GET'])
        ->stateless(true)
    ;

    $routes->add('siganushka_user_putitem', '/users/{id<\d+>}')
        ->controller([UserController::class, 'putItem'])
        ->methods(['PUT', 'PATCH'])
        ->stateless(true)
    ;

    $routes->add('siganushka_user_deleteitem', '/users/{id<\d+>}')
        ->controller([UserController::class, 'deleteItem'])
        ->methods(['DELETE'])
        ->stateless(true)
    ;
};
