<?php
declare(strict_types = 1);

namespace router\collectors;

use ReflectionClass;
use ReflectionMethod;

use router\attributes\Route;
use router\exceptions\UndefinedMethodException;

/**
 * Class RouteCollector 
 * 
 * @package router\collectors
 */
class RouteCollector
{
    /**
     * Variable routes
     * 
     * @var array
     */
    protected array $routes = [];

    /**
     * Variable prefix
     * 
     * - used for routes grouping
     * 
     * @var string
     */
    protected string $prefix = "";

    /**
     * RouteCollector's constructor
     * 
     * @param array $controllers 
     * @return void 
     * @throws UndefinedMethodException 
     */
    public function __construct(
        protected readonly array $controllers,
    ) {
        $this->collectRoutes();
    }

    /**
     * Method getRoutes
     * 
     * @return array 
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Method collectRoutes
     * 
     * @return void 
     * @throws UndefinedMethodException 
     */
    protected function collectRoutes(): void
    {
        foreach ($this->controllers as $controller) {
            $this->prefix = "";

            $controller = new ReflectionClass($controller);
            $this->collectRouteGroupPrefixFromController($controller);
            $this->collectRoutesFromController($controller);
        }
    }

    /**
     * Method collectRoutesFromController
     * 
     * @param ReflectionClass $controller 
     * @return void 
     * @throws UndefinedMethodException 
     */
    protected function collectRoutesFromController(ReflectionClass $controller): void
    {
        $methods = $controller->getMethods();
        
        foreach ($methods as $method) {
            $this->collectRoutesFromMethod($method);
        }
    }

    /**
     * Method collectRouteGroupPrefixFromController
     * 
     * @param ReflectionClass $controller 
     * @return void 
     */
    protected function collectRouteGroupPrefixFromController(ReflectionClass $controller): void
    {
        $attributes = $controller->getAttributes();

        foreach ($attributes as $attribute) {
            $attribute = $attribute->newInstance();

            if (!$attribute instanceof Route) {
                continue;
            }

            $this->prefix = $attribute->path;
        }
    }

    /**
     * collectRoutesFromMethod
     * 
     * @param ReflectionMethod $method 
     * @return void 
     * @throws UndefinedMethodException 
     */
    protected function collectRoutesFromMethod(ReflectionMethod $method): void
    {
        $callback = [ new $method->class, $method->name ];

        $attributes = $method->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute = $attribute->newInstance();

            if (!$attribute instanceof Route) {
                continue;
            }

            $path   = $attribute->path;
            $method = $attribute->method;

            if (!$method) {
                throw new UndefinedMethodException;
            }

            $this->routes[$method][$this->prefix . $path] = $callback;
        }
    }
}
