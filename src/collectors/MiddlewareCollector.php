<?php
declare(strict_types = 1);

namespace Router\Collectors;

use ReflectionClass;
use ReflectionMethod;

use Router\Attributes\Route;
use Router\Exceptions\UndefinedMethodException;

/**
 * Class MiddlewareCollector
 * 
 * @package Router\Collectors
 */
class MiddlewareCollector
{
    /**
     * Variable middlewares
     * 
     * @var array
     */
    protected array $middlewares = [];

    /**
     * Variable prefix
     * 
     * @var string
     */
    protected string $prefix = "";

    /**
     * Variable controllerMiddlewares
     * 
     * @var array
     */
    protected array $controllerMiddlewares = [];

    /**
     * MiddlewareCollector's constructor
     * 
     * @param array $controllers 
     * @return void 
     * @throws UndefinedMethodException 
     */
    public function __construct(
        protected readonly array $controllers,
    ) {
        $this->collectMiddlewares();
    }

    /**
     * Method getMiddlewares
     * 
     * @return array 
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Method collectMiddlewares
     * 
     * @return void 
     * @throws UndefinedMethodException 
     */
    protected function collectMiddlewares(): void
    {
        foreach ($this->controllers as $controller) {
            $controller = new ReflectionClass($controller);
            $this->controllerMiddlewares = [];
            $this->prefix = "";

            $this->collectRouteGroupMiddlewaresFromController($controller);
            $this->collectMiddlewaresFromController($controller);
        }
    }

    /**
     * Method collectMiddlewaresFromController
     * 
     * @param ReflectionClass $controller 
     * @return void 
     * @throws UndefinedMethodException 
     */
    protected function collectMiddlewaresFromController(ReflectionClass $controller): void
    {
        $methods = $controller->getMethods();
        
        foreach ($methods as $method) {
            $this->collectMiddlewaresFromMethod($method);
        }
    }

    /**
     * Method collectRouteGroupMiddlewaresFromController
     * 
     * @param ReflectionClass $controller 
     * @return void 
     */
    protected function collectRouteGroupMiddlewaresFromController(ReflectionClass $controller): void
    {
        $attributes = $controller->getAttributes();

        foreach ($attributes as $attribute) {
            $attribute = $attribute->newInstance();

            if (!$attribute instanceof Route) {
                continue;
            }

            $this->prefix = $attribute->path;
            $middlewares  = $attribute->middlewares;

            foreach ($middlewares as $middleware) {
                $this->controllerMiddlewares[] = $middleware;
            }
        }
    }

    /**
     * Method collectMiddlewaresFromMethod
     * 
     * @param ReflectionMethod $method 
     * @return void 
     * @throws UndefinedMethodException 
     */
    protected function collectMiddlewaresFromMethod(ReflectionMethod $method): void
    {

        $attributes = $method->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute = $attribute->newInstance();
            
            if (!$attribute instanceof Route) {
                continue;
            }

            $path        = $attribute->path;
            $method      = $attribute->method;
            $middlewares = $attribute->middlewares;

            if (!$method) {
                throw new UndefinedMethodException;
            }
            
            $middlewares = array_merge($this->controllerMiddlewares, $middlewares);
            $this->middlewares[$method][$this->prefix . $path] = $middlewares;
        }
    }
}
