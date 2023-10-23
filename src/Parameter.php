<?php
declare(strict_types = 1);

namespace Router;

use Router\Exceptions\InvalidParameterTypeException;

/**
 * Class Parameter
 * 
 * @package Router
 */
final class Parameter
{
    const REGEX_PARAMETER       = "/^\@\[.+?\]$/";
    const REGEX_PARAMETER_PARTS = "/(?<=\@\[)(\w*)(?:(?:\:(?: +)?)(\w*))?(?=\])/";

    const REGEX_INT   = "/^\d*$/";
    const REGEX_FLOAT = "/^\d*([\.|\,]\d*)?$/";

    /**
     * Method resolveParameters
     * 
     * @param array $patterns 
     * @param array $inputs 
     * @return array|null 
     */
    public static function resolveParameters(array $patterns, array $inputs): array|null
    {
        $parameters = [];

        if (sizeof($patterns) !== sizeof($inputs)) {
            return null;
        }

        $combined = array_combine($patterns, $inputs);
        foreach ($combined as $pattern => $input) {
            if ($pattern === $input) {
                continue;
            }

            if (!self::isParameter($pattern)) {
                return null;
            }

            preg_match(self::REGEX_PARAMETER_PARTS, $pattern, $matches);

            $name = $matches[1];
            $type = $matches[2] ?? null;

            $value = self::extractParameterValue($input, $type);

            $parameters[$name] = $value;
        }

        return $parameters;
    }

    /**
     * Method isParameter
     * 
     * @param string $input 
     * @return bool 
     */
    public static function isParameter(string $input): bool
    {
        return preg_match(self::REGEX_PARAMETER, $input) === 1;
    }

    /**
     * Method extractParameterValue
     * 
     * @param string $input 
     * @param null|string $type 
     * @return string|int|float|bool|null 
     * @throws InvalidParameterTypeException 
     */
    protected static function extractParameterValue(string $input, ?string $type): string|int|float|bool|null {
        $value = $input;
        $type  = $type ? strtolower($type) : $type;

        if ($type === "int") {
            preg_match(self::REGEX_INT, $value, $matches);
            $value = (int) $matches[0] ?? null;
        }

        if ($type === "float") {
            preg_match(self::REGEX_FLOAT, $value, $matches);
            $value = (float) str_replace(",", ".", $matches[0]) ?? null;
        }

        if ($type === "bool") {
            if ($value === "true")  return true;
            if ($value === "false") return false;

            $value = null;
        }

        if ($value === null) {
            throw new InvalidParameterTypeException;
        }
        
        return $value;
    }
}
