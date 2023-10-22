<?php
declare(strict_types = 1);

namespace Router;

/**
 * Class Parameter
 * 
 * @package Router
 */
final class Parameter
{
    const REGEX_PARAMETER      = "/^\@\[.+?\]$/";
    const REGEX_PARAMETER_NAME = "/(?<=\@\[).+?(?=\])/";

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

            preg_match(self::REGEX_PARAMETER_NAME, $pattern, $names);
            $name              = $names[0];
            $parameters[$name] = $input;
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
}
