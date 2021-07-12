<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

interface KeyValueRepository
{
    public function has(string $key): bool;

    public function get(string $key): mixed;

    public function set(string $key, $value): void;
}