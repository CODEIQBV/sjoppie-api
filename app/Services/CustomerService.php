<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        private readonly Customer $customer,
        private readonly AddressService $addressService
    ) {}

    public function getAllCustomers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->customer->with('addresses')
            ->latest()
            ->paginate($perPage);
    }

    public function getCustomer(string $id): ?Customer
    {
        return $this->customer->with('addresses')
            ->findOrFail($id);
    }

    public function createCustomer(array $data): Customer
    {
        // Extract address data if present
        $addressData = $data['address'] ?? null;
        unset($data['address']);

        // Create customer
        $customer = $this->customer->create($data);

        // Create address if provided
        if ($addressData) {
            $this->addressService->createAddress($customer->id, $addressData);
        }

        return $customer->load('addresses');
    }

    public function updateCustomer(string $id, array $data): bool
    {
        $customer = $this->getCustomer($id);
        return $customer->update($data);
    }

    public function deleteCustomer(string $id): bool
    {
        $customer = $this->getCustomer($id);
        return $customer->delete();
    }

    public function searchCustomers(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->customer->where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone_number', 'like', "%{$query}%")
                ->orWhere('company', 'like', "%{$query}%")
                ->orWhere('vat_number', 'like', "%{$query}%");
        })
            ->with('addresses')
            ->latest()
            ->paginate($perPage);
    }
}
