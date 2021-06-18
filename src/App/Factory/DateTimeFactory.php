<?php declare(strict_types=1);

namespace Gunratbe\App\Factory;

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

    public static function createFromString(string $datetime): DateTimeInterface
    {
        $timestamp = strtotime($datetime);

        if (false === $timestamp) {
            throw new \Exception('Invalid datetime specified');
        }

        return new \DateTimeImmutable('@' . $timestamp, self::createTimezone());
    }
}