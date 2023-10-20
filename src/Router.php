<?php
declare(strict_types = 1);

namespace router;

use router\collectors\MiddlewareCollector;
use router\collectors\RouteCollector;
use router\exceptions\CallbackException;

/**
 * Class Router
 * 
 * @package router
 */
class Router
{
    /**
     * Variable routes
     * 
     * @var array
     */
    protected readonly array $routes;

    /**
     * Variable middlewares
     * 
     * @var array
     */
    protected readonly array $middlewares;

    /**
     * Router's constructor
     * 
     * @param array $controllers 
     * @return void 
     */
    public function __construct(
        public readonly array $controllers,
    ) {
        $routeCollector = new RouteCollector($controllers);
        $this->routes   = $routeCollector->getRoutes();

        $middlewareCollector = new MiddlewareCollector($controllers); 
        $this->middlewares   = $middlewareCollector->getMiddlewares();
    }

    /**
     * Method resolve
     * 
     * @param string $method 
     * @param string $path 
     * @return mixed 
     * @throws CallbackException 
     */
    public function resolve(string $method, string $path): mixed
    {
        $callback   = $this->routes[$method][$path] ?? null;
        $parameters = [];
        
        if (!$callback) {
            $routes                    = $this->routes[$method];
            [ $callback, $parameters ] = $this->resolveCallback($routes, $path);
            
            if (!$callback) {
                throw new CallbackException;
            }
        }
        
        
        if (!is_callable($callback)) {
            throw new CallbackException;
        }
        
        $middlewaresResults = $this->resolveMiddlewares($this->middlewares[$method], $path);

        return call_user_func($callback, $parameters, $middlewaresResults);
    }

    /**
     * Method resolveCallback
     * 
     * @param array $routes 
     * @param string $path 
     * @return array|null 
     */
    protected function resolveCallback(array $routes, string $path): array|null
    {
        $pathSegments = explode("/", $path);

        foreach ($routes as $route => $callback) {
            $routeSegments = explode("/", $route);

            if (sizeof($routeSegments) !== sizeof($pathSegments)) {
                continue;
            }

            $parameters = Parameter::resolveParameters($routeSegments, $pathSegments);
            if (!$parameters) {
                continue;
            }

            return array($callback, $parameters);
        }

        return null;
    }

    /**
     * Method resolveMiddlewares
     * 
     * @param array $middlewares 
     * @param string $path 
     * @return mixed 
     * @throws CallbackException 
     */
    protected function resolveMiddlewares(array $middlewares, string $path): mixed
    {
        $pathSegments = explode("/", $path);
        $results = [];

        foreach ($middlewares as $middleware => $callbacks) {
            $middlewareSegments = explode("/", $middleware);

            if (sizeof($middlewareSegments) !== sizeof($pathSegments)) {
                continue;
            }

            $parameters = Parameter::resolveParameters($middlewareSegments, $pathSegments);
            $results = array_merge($results, $this->executeMiddlewares($callbacks, $parameters));
        }

        return $results;
    }

    /**
     * Method executeMiddlewares
     * 
     * @param array $callbacks 
     * @param array $parameters 
     * @return array 
     * @throws CallbackException 
     */
    protected function executeMiddlewares(array $callbacks, array $parameters): array {
        $results = [];
        
        foreach ($callbacks as $callback) {
            if (!is_callable($callback)) {
                $callback = [new $callback[0], $callback[1]];

                if (!is_callable($callback)) {
                    throw new CallbackException;
                }
            }

            $results[] = call_user_func($callback);
        }
        
        return $results;
    }
}
