<?php

namespace App\Traits;

use DateTimeZone;

/**
 * Trait for datetime range value
 */
trait DatetimeRange
{
    /**
     * Convert datetime range from string by format
     *
     * @param string $datetimeRange
     * @param string $format
     * @return array|false
     */
    public function getDatetimeRangeFromFormat(string $datetimeRange, string $format)
    {
        $dates = explode(' - ', $datetimeRange);
        if (count($dates) != 2) {
            return false;
        }

        $start = date_create_from_format($format, reset($dates), new DateTimeZone('Europe/Moscow'));
        $end = date_create_from_format($format, end($dates), new DateTimeZone('Europe/Moscow'));
        if (!$start || !$end) {
            return false;
        }

        return [$start, $end];
    }
}