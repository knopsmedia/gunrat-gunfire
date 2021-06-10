<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Model;

final class Image
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}