<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Traits;

use Cocur\Slugify\Slugify;
use Cocur\Slugify\SlugifyInterface;

trait CreateSlugTrait
{
    private static ?SlugifyInterface $slugify = null;

    private function createSlug(string $input): string
    {
        if (null === self::$slugify) {
            self::$slugify = new Slugify();
        }

        return self::$slugify->slugify($input);
    }
}