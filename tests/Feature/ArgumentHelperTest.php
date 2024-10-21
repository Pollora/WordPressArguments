<?php

use Pollora\WordPressArgs\ArgumentHelper;

class TestClass
{
    use ArgumentHelper;

    private $testProperty;
    private $anotherProperty;

    public function getTestProperty()
    {
        return $this->testProperty;
    }

    public function setTestProperty($value)
    {
        $this->testProperty = $value;
        return $this;
    }

    public function getAnotherProperty()
    {
        return $this->anotherProperty;
    }

    public function setAnotherProperty($value)
    {
        $this->anotherProperty = $value;
        return $this;
    }
}

test('extractArgumentFromProperties correctly extracts arguments', function () {
    $testObject = new TestClass();
    $testObject->setTestProperty('test value');
    $testObject->setAnotherProperty('another value');

    $args = $testObject->extractArgumentFromProperties();

    expect($args)->toBe([
        'test_property' => 'test value',
        'another_property' => 'another value',
    ]);
});

test('buildArguments correctly merges raw arguments', function () {
    $testObject = new TestClass();
    $testObject->setTestProperty('test value');
    $testObject->setRawArgs(['raw_arg' => 'raw value']);

    // Simulons la fonction wp_parse_args de WordPress
    if (!function_exists('wp_parse_args')) {
        function wp_parse_args($args, $defaults = []) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
            return array_merge($defaults, $r);
        }
    }

    $args = invade($testObject)->buildArguments();

    expect($args)->toBe([
        'test_property' => 'test value',
        'raw_arg' => 'raw value',
    ]);
});

test('setRawArgs and getRawArgs work correctly', function () {
    $testObject = new TestClass();
    $rawArgs = ['test' => 'value'];

    $testObject->setRawArgs($rawArgs);

    expect($testObject->getRawArgs())->toBe($rawArgs);
});

test('collectGetters returns correct getters', function () {
    $testObject = new TestClass();

    $getters = (new ReflectionClass($testObject))->getMethod('collectGetters')->invoke($testObject);

    expect($getters)->toBe(['testProperty', 'anotherProperty']);
});

test('makeArgName correctly converts to snake_case', function () {
    $testObject = new TestClass();

    $argName = (new ReflectionClass($testObject))->getMethod('makeArgName')->invoke($testObject, 'testProperty');

    expect($argName)->toBe('test_property');
});