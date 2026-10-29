<?php

declare(strict_types=1);

namespace Pollora\WordPressArgs;

use Illuminate\Support\Str;

/**
 * The ArgumentHelper class is a trait that provides methods to extract arguments from properties using getter methods.
 */
trait ArgumentHelper
{
    /**
     * Raw post type args.
     *
     * @var args
     */
    private $rawArgs;

    /**
     * Set the raw arguments for the method.
     *
     * @param  array  $rawArgs  The raw arguments to be set.
     * @return self Returns the instance of the object for method chaining.
     */
    public function setRawArgs(array $rawArgs): self
    {
        $this->rawArgs = $rawArgs;

        return $this;
    }

    /**
     * Get the raw arguments
     *
     * @return array|null The raw arguments, or null if not set
     */
    public function getRawArgs(): ?array
    {
        return $this->rawArgs;
    }

    /**
     * Extracts arguments from object properties using getter methods.
     *
     * @return array An associative array containing the extracted arguments.
     */
    public function extractArgumentFromProperties(): array
    {
        $args = [];
        $getters = $this->collectGetters();

        foreach ($getters as $getter) {
            $argValue = $this->{$getter};
            if ($argValue === null) {
                continue;
            }

            $args[$this->makeArgName($getter)] = $argValue;
        }

        return $args;
    }

    /**
     * Build the arguments array
     *
     * @return array The built arguments array
     */
    protected function buildArguments(): array
    {
        $args = $this->extractArgumentFromProperties();

        return wp_parse_args($this->getRawArgs() ?? [], $args);
    }

    /**
     * Collects all getter methods of the current object.
     *
     * @return array An array containing all getter method names.
     */
    private function collectGetters(): array
    {
        $methodProperties = array_keys(get_class_vars($this::class));

        return array_filter($methodProperties, fn (int|string $propertyName): bool => $propertyName !== 'rawArgs');
    }

    /**
     * Generate argument name from given getter method
     *
     * @return string Argument's name in snake_case format
     */
    private function makeArgName(string $propertyName): string
    {
        return Str::snake($propertyName);
    }
}
