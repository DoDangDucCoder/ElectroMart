<?php

namespace App\Application\Customer\Services;

use App\Infrastructure\Persistence\Eloquent\Customer\Address;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CustomerService
{
    public function createCustomer(array $data): Customer
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'avatar_path' => $data['avatar_path'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'meta' => $data['meta'] ?? null,
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'phone_verified_at' => $data['phone_verified_at'] ?? null,
        ];

        return Customer::create($payload);
    }

    public function updateCustomer(int $customerId, array $data): Customer
    {
        $customer = Customer::findOrFail($customerId);

        $payload = Arr::only($data, [
            'name',
            'email',
            'phone',
            'password',
            'avatar_path',
            'is_active',
            'meta',
            'email_verified_at',
            'phone_verified_at',
            'loyalty_tier_id',
            'loyalty_points_balance',
        ]);

        $customer->fill($payload);
        $customer->save();

        return $customer;
    }

    public function registerCustomer(array $data): Customer
    {
        return $this->createCustomer($data);
    }

    public function updateCustomerProfile(array $data): ?Customer
    {
        $customerId = $data['customer_id'] ?? $data['id'] ?? null;

        if (! $customerId) {
            return null;
        }

        return $this->updateCustomer((int) $customerId, $data);
    }

    public function manageCustomerAddress(array $data): ?Address
    {
        $customerId = $data['customer_id'] ?? null;

        if (! $customerId) {
            return null;
        }

        $addressId = $data['address_id'] ?? $data['id'] ?? null;
        $address = $addressId
            ? Address::where('customer_id', $customerId)->find($addressId)
            : new Address(['customer_id' => $customerId]);

        if (! $address) {
            return null;
        }

        $payload = Arr::only($data, [
            'label',
            'recipient_name',
            'recipient_phone',
            'email',
            'country',
            'location_city_id',
            'location_province_id',
            'location_ward_id',
            'postal_code',
            'address_line1',
            'address_line2',
            'is_default',
            'meta',
        ]);

        if (! $addressId) {
            $payload['customer_id'] = $customerId;
        }

        $address->fill($payload);
        $address->save();

        return $address;
    }

    public function getCustomerOrderHistory(array $data): Collection
    {
        $customerId = $data['customer_id'] ?? $data['id'] ?? null;

        if (! $customerId) {
            return collect();
        }

        return Order::with(['items', 'shipping', 'payments'])
            ->where('customer_id', (int) $customerId)
            ->orderByDesc('id')
            ->get();
    }
}
