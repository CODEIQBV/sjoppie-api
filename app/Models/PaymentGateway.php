<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class PaymentGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'module_name',
        'is_active',
        'is_test_mode',
        'configuration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
    ];

    public function getConfigurationAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        try {
            $decrypted = Crypt::decryptString($value);
            return json_decode($decrypted, true) ?? [];
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt configuration: ' . $e->getMessage());
            return [];
        }
    }

    public function setConfigurationAttribute($value)
    {
        if (!is_array($value)) {
            $value = [];
        }

        try {
            $json = json_encode($value);
            if ($json === false) {
                throw new \RuntimeException('Failed to encode configuration to JSON');
            }
            $this->attributes['configuration'] = Crypt::encryptString($json);
        } catch (\Exception $e) {
            \Log::error('Failed to encrypt configuration: ' . $e->getMessage());
            $this->attributes['configuration'] = Crypt::encryptString(json_encode([]));
        }
    }

    /**
     * Get a specific configuration value
     */
    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->configuration[$key] ?? $default;
    }

    /**
     * Check if a specific configuration value exists
     */
    public function hasConfigValue(string $key): bool
    {
        return isset($this->configuration[$key]);
    }
}
