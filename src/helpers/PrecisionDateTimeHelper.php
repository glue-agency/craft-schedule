<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\helpers;

use Craft;
use DateTime;
use DateTimeZone;

/**
 * Class DateTimeHelper
 *
 * @package GlueAgency\schedule\helpers
 * @author Glue Agency <info@glue.be>
 */
class PrecisionDateTimeHelper
{
    // Static Methods
    // =========================================================================

    /**
     * @return int
     */
    public static function time(): int
    {
        return round(microtime(true) * 1000);
    }

    /**
     * @param string|int $value
     * @param bool $setToSystemTimeZone
     * @return DateTime
     */
    public static function toDateTime($value, bool $setToSystemTimeZone = true): DateTime
    {
        $timestamp = substr($value, 0, -3);
        $datetime = new DateTime("@$timestamp");

        if ($setToSystemTimeZone) {
            $datetime->setTimezone(new DateTimeZone(Craft::$app->getTimeZone()));
        }

        return $datetime;
    }

    /**
     * @param string $format
     * @param mixed $value
     * @param bool $setToSystemTimeZone
     * @return string
     */
    public static function format(string $format, $value, bool $setToSystemTimeZone = true): string
    {
        $timestamp = substr($value, 0, -3);
        $microsecond = substr($value, -3);

        $datetime = new DateTime("@$timestamp");
        if ($setToSystemTimeZone) {
            $datetime->setTimezone(new DateTimeZone(Craft::$app->getTimeZone()));
        }

        return $datetime->format(preg_replace('#s#', 's.' . $microsecond, $format));
    }
}
