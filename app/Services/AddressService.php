<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    public function __construct(
        private readonly Address $address
    ) {}

    public function getCustomerAddresses(string $customerId): Collection
    {
        return $this->address->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    public function getAddress(string $id): ?Address
    {
        return $this->address->findOrFail($id);
    }

    public function createAddress(string $customerId, array $data): Address
    {
        // If this is set as default, unset default for other addresses of the same type
        if ($data['is_default'] ?? false) {
            $this->address->where('customer_id', $customerId)
                ->where('type', $data['type'])
                ->update(['is_default' => false]);
        }

        return $this->address->create([
            ...$data,
            'customer_id' => $customerId,
        ]);
    }

    public function updateAddress(string $id, array $data): bool
    {
        $address = $this->getAddress($id);

        // If this is set as default, unset default for other addresses of the same type
        if ($data['is_default'] ?? false) {
            $this->address->where('customer_id', $address->customer_id)
                ->where('type', $data['type'] ?? $address->type)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        return $address->update($data);
    }

    public function deleteAddress(string $id): bool
    {
        $address = $this->getAddress($id);
        return $address->delete();
    }

    public function getDefaultAddress(string $customerId, string $type): ?Address
    {
        return $this->address->where('customer_id', $customerId)
            ->where('type', $type)
            ->where('is_default', true)
            ->first();
    }
} 