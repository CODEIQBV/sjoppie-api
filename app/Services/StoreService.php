<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Cache;

class StoreService
{
    /**
     * Get the current store details.
     *
     * @return Store
     */
    public function getCurrentStore(): Store
    {
        return Cache::remember('store_details', 3600, function () {
            return Store::first();
        });
    }

    /**
     * Update store details.
     *
     * @param array $data
     * @return Store
     */
    public function updateStore(array $data): Store
    {
        $store = $this->getCurrentStore();
        $store->update($data);
        
        Cache::forget('store_details');
        
        return $store->fresh();
    }
} 