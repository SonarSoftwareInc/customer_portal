<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait Throttles
{
    /**
     * Get the current throttle value
     *
     * @return mixed
     */
    protected function getThrottleValue($throttleName, $tag)
    {
        return Cache::tags("$throttleName.throttle")->get($tag);
    }

    /**
     * Increment the throttle value
     */
    protected function incrementThrottleValue($throttleName, $tag)
    {
        Cache::tags("$throttleName.throttle")->put(
            $tag,
            (int) Cache::tags("$throttleName.throttle")->get($tag) + 1,
            Carbon::now()->addMinutes(2)
        );
    }

    /**
     * Reset the throttle value
     */
    protected function resetThrottleValue($throttleName, $tag)
    {
        Cache::tags("$throttleName.throttle")->forget($tag);
    }
}
