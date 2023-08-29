<?php

namespace App\Services;

use Carbon\Carbon;

class FormattingService
{
    /**
     * Format a value to currency string
     */
    public function currency($value): string
    {
        return config('customer_portal.currency_symbol').number_format($value, 2, config('customer_portal.decimal_separator'), config('customer_portal.thousands_separator'));
    }

    /**
     * Format a date into a standard date format, optionally converting from UTC to the local timezone
     *
     * @return mixed
     */
    public function date($value, bool $convertFromUtc = false)
    {
        if ($convertFromUtc === true) {
            $carbon = new Carbon($value, 'UTC');
            $carbon->tz(config('app.timezone'));
        } else {
            $carbon = new Carbon($value, config('app.timezone'));
        }

        $formattedMonth = utrans('months.'.$carbon->month);

        return "$formattedMonth {$carbon->day}, {$carbon->year}";
    }

    /**
     * Format a date and time into a standard datetime format, optionally converting from UTC to the local timezone
     *
     * @return mixed
     */
    public function datetime($value, bool $convertFromUtc = false)
    {
        if ($convertFromUtc === true) {
            $carbon = new Carbon($value, 'UTC');
            $carbon->tz(config('app.timezone'));
        } else {
            $carbon = new Carbon($value, config('app.timezone'));
        }

        $formattedMonth = utrans('months.'.$carbon->month);

        return "$formattedMonth {$carbon->day}, {$carbon->year} {$carbon->hour}:{$carbon->minute}";
    }
}
