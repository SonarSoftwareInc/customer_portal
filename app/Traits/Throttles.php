<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Throttles
{
    /**
     * Get the current throttle value
     * @param $throttleName
     * @param $tag
     * @return mixed
     */
    protected function getThrottleValue($throttleName, $tag)
    {
        return Cache::tags("$throttleName.throttle")->get($tag);
    }

    /**
     * Increment the throttle value
     * @param $throttleName
     * @param $tag
     */
    protected function incrementThrottleValue($throttleName, $tag)
    {
        Cache::tags("$throttleName.throttle")->put(
            $tag,
            (int)Cache::tags("$throttleName.throttle")->get($tag)+1,
            2
        );
    }

    /**
     * Reset the throttle value
     * @param $throttleName
     * @param $tag
     */
    protected function resetThrottleValue($throttleName, $tag)
    {
        Cache::tags("$throttleName.throttle")->forget($tag);
    }
}
