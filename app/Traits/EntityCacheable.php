<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait EntityCacheable
{
    public function caching(
        bool $canEntityCache,
        string $cacheKey,
        \Closure $data,
        ?Carbon $cacheTime = null
    ): mixed {
        $cachedData = null;

        if ($canEntityCache) {
            $cachedData = Cache::get($cacheKey);
        }

        if (is_null($cachedData)) {
            $cachedData = $data();

            if ($canEntityCache) {
                if (is_null($cacheTime)) {
                    $cacheTime = now()->addDay();
                }

                Cache::put($cacheKey, $cachedData, $cacheTime);
            }
        }

        return $cachedData;
    }
}
