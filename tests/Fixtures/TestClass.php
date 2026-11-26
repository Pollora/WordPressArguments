<?php

declare(strict_types=1);

use Pollora\WordPressArgs\ArgumentHelper;

/*
 * The class the tests exercise the trait through. PHPStan analyses a trait only
 * through a class that uses it, so this directory is part of the analysed paths.
 */

class TestClass
{
    use ArgumentHelper;

    private ?string $testProperty = null;

    private ?string $anotherProperty = null;

    public function getTestProperty(): ?string
    {
        return $this->testProperty;
    }

    public function setTestProperty(string $value): self
    {
        $this->testProperty = $value;

        return $this;
    }

    public function getAnotherProperty(): ?string
    {
        return $this->anotherProperty;
    }

    public function setAnotherProperty(string $value): self
    {
        $this->anotherProperty = $value;

        return $this;
    }
}
