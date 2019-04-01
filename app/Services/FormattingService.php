<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class FormattingService
{
    /**
     * Format a value to currency string
     * @param $value
     * @return string
     */
    public function currency($value)
    {
        return config("customer_portal.currency_symbol") . number_format($value, 2, config("customer_portal.decimal_separator"), config("customer_portal.thousands_separator"));
    }

    /**
     * Format a date into a standard date format, optionally converting from UTC to the local timezone
     * @param $value
     * @param bool $convertFromUtc
     * @return mixed
     */
    public function date($value, $convertFromUtc = false)
    {
        if ($convertFromUtc === true) {
            $carbon = new Carbon($value, "UTC");
            $carbon->tz(config("app.timezone"));
        } else {
            $carbon = new Carbon($value, config("app.timezone"));
        }

        $formattedMonth = utrans("months." . $carbon->month);
        return "$formattedMonth {$carbon->day}, {$carbon->year}";
    }

    /**
     * Format a date and time into a standard datetime format, optionally converting from UTC to the local timezone
     * @param $value
     * @param bool $convertFromUtc
     * @return mixed
     */
    public function datetime($value, $convertFromUtc = false)
    {
        if ($convertFromUtc === true) {
            $carbon = new Carbon($value, "UTC");
            $carbon->tz(config("app.timezone"));
        } else {
            $carbon = new Carbon($value, config("app.timezone"));
        }

        $formattedMonth = utrans("months." . $carbon->month);
        return "$formattedMonth {$carbon->day}, {$carbon->year} {$carbon->hour}:{$carbon->minute}";
    }
}
