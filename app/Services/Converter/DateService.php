<?php

namespace App\Services\Converter;

class DateService
{
    public function reformatDate(string $date, string $format): string
    {
        // Try to parse the date
        $timestamp = strtotime($date);

        // If parsing failed, return empty string
        if ($timestamp === false) {
            return '';
        }

        // Format the date according to the specified format
        return date($format, $timestamp);
    }
}
