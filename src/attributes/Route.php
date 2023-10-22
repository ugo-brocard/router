<?php
declare(strict_types = 1);

namespace Router\Attributes;

use Attribute;

/**
 * Class Route (attribute)
 * 
 * @package Router\Attributes
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * Route attribute's constructor
     * 
     * @param string $path 
     * @param null|string $method 
     * @param array $middlewares 
     * @return void 
     */
    public function __construct(
        public string  $path,
        public ?string $method      = null,
        public array   $middlewares = [],
    ) {}
}
