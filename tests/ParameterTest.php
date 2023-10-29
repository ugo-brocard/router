<?php
declare(strict_types = 1);

namespace Router\Tests;

use PHPUnit\Framework\TestCase;
use Router\Parameter;

class ParameterTest extends TestCase
{
    public function testResolveParametersBasic(): void
    {
        $patterns = ["user", "@[id]", "name"];
        $inputs   = ["user", "15234", "name"];

        $parameters = Parameter::resolveParameters($patterns, $inputs);
        $expected   = [
            "id" => "15234"
        ];

        $this->assertEquals($expected, $parameters);
        $this->assertIsString($parameters["id"]);
    }

    public function testResolveParametersInteger(): void
    {
        $patterns = ["user", "@[id: int]", "name"];
        $inputs   = ["user", "15234", "name"];

        $parameters = Parameter::resolveParameters($patterns, $inputs);
        $expected   = [
            "id" => 15234
        ];
        
        $this->assertEquals($expected, $parameters);
        $this->assertIsInt($parameters["id"]);
    }

    public function testResolveParametersFloat(): void
    {
        $patterns = ["user", "@[id: float]", "name"];
        $inputs   = ["user", "15234", "name"];

        $parameters = Parameter::resolveParameters($patterns, $inputs);
        $expected   = [
            "id" => 15234.0
        ];
        
        $this->assertEquals($expected, $parameters);
        $this->assertIsFloat($parameters["id"]);
    }

    public function testResolveParametersBoolean(): void
    {
        $patterns = ["user", "@[isValid: bool]", "name"];
        $inputs   = ["user", "true", "name"];

        $parameters = Parameter::resolveParameters($patterns, $inputs);
        $expected   = [
            "isValid" => true
        ];
        
        $this->assertEquals($expected, $parameters);
        $this->assertIsBool($parameters["isValid"]);
    }

    public function testResolveParametersNotMatchingArrays(): void
    {
        $patterns = ["user", "@[id]", "name"];
        $inputs   = ["user", "true"];

        $parameters = Parameter::resolveParameters($patterns, $inputs);
        
        $this->assertNull($parameters);
    }

    public function testResolveParametersNoParameters(): void
    {
        $patterns = ["user", "name"];
        $inputs   = ["user", "name"];

        $parameters = Parameter::resolveParameters($patterns, $inputs);
        
        $this->assertIsArray($parameters);
        $this->assertEmpty($parameters);
    }

    public function testResolveParametersInputContainsParameter(): void
    {
        $patterns = ["user", "@[test]", "name"];
        $inputs   = ["user", "@[test]", "name"];

        $parameters = Parameter::resolveParameters($patterns, $inputs);
        
        $this->assertEmpty($parameters);
    }

    public function test(): void
    {
        $this->assertEquals(true, true);
    }

    public function testIsParameter(): void 
    {
        $input = "@[id]";
        $isParameter = Parameter::isParameter($input);

        $this->assertTrue($isParameter);
    }

    public function testIsParameterWithType(): void 
    {
        $input = "@[id: int]";
        $isParameter = Parameter::isParameter($input);

        $this->assertTrue($isParameter);
    }

    public function testIsNotParameter(): void
    {
        $input = "id";
        $isParameter = Parameter::isParameter($input);

        $this->assertFalse($isParameter);
    }
}
