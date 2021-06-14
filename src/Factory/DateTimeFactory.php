<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Factory;

use DateTimeInterface;
use DateTimeZone;

final class DateTimeFactory
{
    public static function createTimezone(): DateTimeZone
    {
        return new DateTimeZone('Europe/Brussels');
    }

    public static function now(): DateTimeInterface
    {
        return new \DateTimeImmutable('now', self::createTimezone());
    }

    public static function createFromFormat(string $datetime, string $format = 'Y-m-d H:i:s'): DateTimeInterface
    {
        return \DateTimeImmutable::createFromFormat($format, $datetime, self::createTimezone());
    }
}