<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Slim;

use DI\Attribute\Inject                                as Inject;
use GAState\Web\LTI\Controller\JWKSController          as JWKSController;
use GAState\Web\LTI\Controller\LaunchController        as LaunchController;
use GAState\Web\LTI\Controller\LoginController         as LoginController;
use GAState\Web\LTI\Middleware\LaunchMessageMiddleware as LaunchMessageMiddleware;
use GAState\Web\Slim\App                               as SlimApp;
use GAState\Web\Slim\Middleware\ErrorMiddleware        as ErrorMiddleware;
use GAState\Web\Slim\Middleware\SessionMiddleware      as SessionMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface                as Middleware;
use Slim\Interfaces\RouteCollectorProxyInterface       as RouteContainer;
use Slim\Middleware\BodyParsingMiddleware              as SlimBodyParsingMiddleware;
use Slim\Middleware\ContentLengthMiddleware            as SlimContentLengthMiddleware;
use Slim\Middleware\RoutingMiddleware                  as SlimRoutingMiddleware;

class App extends SlimApp
{
    #[Inject]
    protected ?LaunchMessageMiddleware $launchMessageMiddleware = null;


    /**
     * @param array<string,Middleware|null> $middleware
     *
     * @return void
     */
    protected function loadMiddleware(array $middleware): void
    {
        parent::loadMiddleware([
            LaunchMessageMiddleware::class     => $this->launchMessageMiddleware,
            SlimBodyParsingMiddleware::class   => $middleware[SlimBodyParsingMiddleware::class] ?? null,
            SessionMiddleware::class           => $middleware[SessionMiddleware::class] ?? null,
            SlimRoutingMiddleware::class       => $middleware[SlimRoutingMiddleware::class] ?? null,
            ErrorMiddleware::class             => $middleware[ErrorMiddleware::class] ?? null,
            SlimContentLengthMiddleware::class => $middleware[SlimContentLengthMiddleware::class] ?? null,
        ]);
    }


    /**
     * @param RouteContainer<ContainerInterface> $routes
     *
     * @return void
     */
    protected function loadRoutes(RouteContainer $routes): void
    {
        $routes->get('/lti/jwks', join([JWKSController::class, 'jwks']));
        $routes->post('/lti/login', join([LoginController::class, 'login']));
        $routes->post('/lti/launch', join([LaunchController::class, 'launch']));
        $routes->get('/lti/launch', join([LaunchController::class, 'getMessage']));
    }
}
