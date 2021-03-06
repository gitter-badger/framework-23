<?php
declare(strict_types = 1);

namespace Tests\Dependency\Doubles;

class DependencyE
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }
}
