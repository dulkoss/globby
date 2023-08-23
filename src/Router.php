<?php

namespace Globby;

use Globby\Contracts\Application;
use Globby\Contracts\Middleware;
use Globby\Contracts\Router as RouterContract;
use Globby\Exceptions\RouteNotFoundException;
use Globby\Middleware\RoutingMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements Application, RouterContract
{
    /**
     * @var array<string, callable>
     */
    protected array $routes = [];

    /**
     * @var array<int, Middleware>
     */
    protected array $stack = [];

    public function __construct()
    {
        $this->layer(new RoutingMiddleware($this));
    }

    public function route(callable $route): static
    {
        $meta = Route::fromCallable($route);

        $this->routes["{$meta->uri()}:{$meta->method()->name}"] = $route;

        return $this;
    }

    public function getHandler(string $uri, string $method): callable
    {
        return $this->routes["$uri:$method"] ?? throw new RouteNotFoundException;
    }

    public function layer(Middleware $middleware): static
    {
        $this->stack[] = $middleware;

        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $stack = new Stack(array_reverse($this->stack));

        return $stack->next($request);
    }
}
