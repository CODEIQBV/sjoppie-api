<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Modules\PaymentGateways\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use Throwable;

class PaymentGatewayService
{
    public function getAvailableModules(): Collection
    {
        try {
            $modules = collect();
            $path = app_path('Modules/PaymentGateways');
            
            foreach (File::directories($path) as $directory) {
                $moduleName = basename($directory);
                if ($moduleName === 'Contracts') {
                    continue;
                }

                $className = "App\\Modules\\PaymentGateways\\{$moduleName}\\{$moduleName}PaymentGateway";
                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);
                    if ($reflection->implementsInterface(PaymentGatewayInterface::class)) {
                        $instance = app($className);
                        $modules->push([
                            'name' => $instance->getName(),
                            'module_name' => $moduleName,
                            'description' => $instance->getDescription(),
                            'configuration' => $instance->getRequiredConfiguration(),
                        ]);
                    }
                }
            }

            return $modules;
        } catch (Throwable $e) {
            Log::error('Error getting available modules: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function createGateway(array $data): PaymentGateway
    {
        try {
            // Ensure configuration is properly formatted
            if (isset($data['configuration']) && is_array($data['configuration'])) {
                $data['configuration'] = $this->sanitizeConfiguration($data['configuration']);
            }

            $gateway = new PaymentGateway($data);
            $gateway->save();
            return $gateway;
        } catch (Throwable $e) {
            Log::error('Error creating payment gateway: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $this->sanitizeLogData($data),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function updateGateway(PaymentGateway $gateway, array $data): PaymentGateway
    {
        try {
            // Ensure configuration is properly formatted
            if (isset($data['configuration']) && is_array($data['configuration'])) {
                $data['configuration'] = $this->sanitizeConfiguration($data['configuration']);
            }

            $gateway->update($data);
            return $gateway->fresh();
        } catch (Throwable $e) {
            Log::error('Error updating payment gateway: ' . $e->getMessage(), [
                'exception' => $e,
                'gateway_id' => $gateway->id,
                'data' => $this->sanitizeLogData($data),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getGatewayInstance(PaymentGateway $gateway): ?PaymentGatewayInterface
    {
        try {
            $className = "App\\Modules\\PaymentGateways\\{$gateway->module_name}\\{$gateway->module_name}PaymentGateway";
            
            if (class_exists($className)) {
                $instance = app($className);
                $instance->initialize($gateway->configuration);
                return $instance;
            }

            return null;
        } catch (Throwable $e) {
            Log::error('Error getting gateway instance: ' . $e->getMessage(), [
                'exception' => $e,
                'gateway_id' => $gateway->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Sanitize configuration data before saving
     */
    protected function sanitizeConfiguration(array $configuration): array
    {
        // Remove any empty values
        $configuration = array_filter($configuration, function ($value) {
            return $value !== null && $value !== '';
        });

        return $configuration;
    }

    /**
     * Sanitize data for logging (remove sensitive information)
     */
    protected function sanitizeLogData(array $data): array
    {
        if (isset($data['configuration'])) {
            $data['configuration'] = array_map(function ($value) {
                if (is_string($value) && strlen($value) > 10) {
                    return '***REDACTED***';
                }
                return $value;
            }, $data['configuration']);
        }

        return $data;
    }
} 