<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Model;

final class Attribute
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->value = $value;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}