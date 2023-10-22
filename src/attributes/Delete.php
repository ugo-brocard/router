<?php
declare(strict_types = 1);

namespace Router\Attributes;

use Attribute;

/**
 * Class Delete (attribute)
 * @package Router\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Delete extends Route
{
    /**
     * Delete attribute's constructor
     * 
     * @param string $path 
     * @param array $middlewares 
     * @return void 
     */
    public function __construct(string $path, array $middlewares = []) {
        parent::__construct($path, "delete", $middlewares);
    }
}
