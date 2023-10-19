<?php
declare(strict_types = 1);

namespace router\attributes;

use Attribute;

/**
 * Class Get (attribute)
 * @package router\attributes
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Get extends Route
{
    /**
     * Get attribute's constructor
     * 
     * @param string $path 
     * @param array $middlewares 
     * @return void 
     */
    public function __construct(string $path, array $middlewares = []) {
        parent::__construct($path, "get", $middlewares);
    }
}
