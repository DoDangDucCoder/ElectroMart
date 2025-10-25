<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class DataEncryptionService
{
    private string $encryptionKey;
    private string $encryptionCipher;
    private array $encryptedFields = [];
    private array $sensitiveFields = [];

    public function __construct()
    {
        $this->encryptionKey = Config::get('app.key');
        $this->encryptionCipher = Config::get('app.cipher', 'AES-256-CBC');
        $this->initializeSensitiveFields();
    }

    private function initializeSensitiveFields(): void
    {
        $this->sensitiveFields = [
            'customers' => [
                'email', 'phone', 'address_line1', 'address_line2',
                'city', 'postal_code', 'date_of_birth', 'ssn',
            ],
            'orders' => [
                'customer_email', 'shipping_address', 'billing_address',
                'payment_method', 'payment_details',
            ],
            'products' => [
                'cost_price', 'supplier_info', 'internal_notes',
            ],
            'users' => [
                'email', 'phone', 'address', 'emergency_contact',
            ],
        ];

        $this->encryptedFields = [
            'customers' => ['ssn', 'date_of_birth'],
            'orders' => ['payment_details'],
            'products' => ['cost_price', 'supplier_info'],
            'users' => ['emergency_contact'],
        ];
    }

    public function encryptField(string $table, string $field, $value): string
    {
        if (empty($value)) {
            return $value;
        }

        try {
            $encryptedValue = Crypt::encryptString($value);
            
            Log::debug('Field encrypted', [
                'table' => $table,
                'field' => $field,
                'timestamp' => now(),
            ]);

            return $encryptedValue;
        } catch (\Exception $e) {
            Log::error('Encryption failed', [
                'table' => $table,
                'field' => $field,
                'error' => $e->getMessage(),
            ]);
            
            throw new \Exception("Failed to encrypt field: {$e->getMessage()}");
        }
    }

    public function decryptField(string $table, string $field, string $encryptedValue)
    {
        if (empty($encryptedValue)) {
            return $encryptedValue;
        }

        try {
            $decryptedValue = Crypt::decryptString($encryptedValue);
            
            Log::debug('Field decrypted', [
                'table' => $table,
                'field' => $field,
                'timestamp' => now(),
            ]);

            return $decryptedValue;
        } catch (\Exception $e) {
            Log::error('Decryption failed', [
                'table' => $table,
                'field' => $field,
                'error' => $e->getMessage(),
            ]);
            
            throw new \Exception("Failed to decrypt field: {$e->getMessage()}");
        }
    }

    public function encryptRecord(array $data, string $table): array
    {
        $encryptedData = $data;
        $encryptedFields = $this->encryptedFields[$table] ?? [];

        foreach ($encryptedFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $encryptedData[$field] = $this->encryptField($table, $field, $data[$field]);
            }
        }

        return $encryptedData;
    }

    public function decryptRecord(array $data, string $table): array
    {
        $decryptedData = $data;
        $encryptedFields = $this->encryptedFields[$table] ?? [];

        foreach ($encryptedFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                try {
                    $decryptedData[$field] = $this->decryptField($table, $field, $data[$field]);
                } catch (\Exception $e) {
                    Log::warning('Failed to decrypt field, keeping encrypted value', [
                        'table' => $table,
                        'field' => $field,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $decryptedData;
    }

    public function maskSensitiveData(array $data, string $table): array
    {
        $maskedData = $data;
        $sensitiveFields = $this->sensitiveFields[$table] ?? [];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $maskedData[$field] = $this->maskField($data[$field], $field);
            }
        }

        return $maskedData;
    }

    private function maskField($value, string $field): string
    {
        $value = (string) $value;
        $length = strlen($value);

        if ($length <= 2) {
            return str_repeat('*', $length);
        }

        switch ($field) {
            case 'email':
                return $this->maskEmail($value);
            case 'phone':
                return $this->maskPhone($value);
            case 'ssn':
                return $this->maskSSN($value);
            case 'credit_card':
                return $this->maskCreditCard($value);
            default:
                return $this->maskGeneric($value);
        }
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $this->maskGeneric($email);
        }

        $username = $parts[0];
        $domain = $parts[1];

        if (strlen($username) <= 2) {
            $maskedUsername = str_repeat('*', strlen($username));
        } else {
            $maskedUsername = $username[0] . str_repeat('*', strlen($username) - 2) . $username[-1];
        }

        return $maskedUsername . '@' . $domain;
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($digits);

        if ($length <= 4) {
            return str_repeat('*', strlen($phone));
        }

        $masked = str_repeat('*', $length - 4) . substr($digits, -4);
        
        // Preserve original format
        $formatted = $phone;
        $maskedIndex = 0;
        
        for ($i = 0; $i < strlen($phone); $i++) {
            if (is_numeric($phone[$i])) {
                $formatted[$i] = $masked[$maskedIndex];
                $maskedIndex++;
            }
        }

        return $formatted;
    }

    private function maskSSN(string $ssn): string
    {
        $digits = preg_replace('/[^0-9]/', '', $ssn);
        
        if (strlen($digits) !== 9) {
            return str_repeat('*', strlen($ssn));
        }

        return '***-**-' . substr($digits, -4);
    }

    private function maskCreditCard(string $card): string
    {
        $digits = preg_replace('/[^0-9]/', '', $card);
        $length = strlen($digits);

        if ($length < 4) {
            return str_repeat('*', strlen($card));
        }

        $masked = str_repeat('*', $length - 4) . substr($digits, -4);
        
        // Preserve original format
        $formatted = $card;
        $maskedIndex = 0;
        
        for ($i = 0; $i < strlen($card); $i++) {
            if (is_numeric($card[$i])) {
                $formatted[$i] = $masked[$maskedIndex];
                $maskedIndex++;
            }
        }

        return $formatted;
    }

    private function maskGeneric(string $value): string
    {
        $length = strlen($value);
        
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, 2) . str_repeat('*', $length - 4) . substr($value, -2);
    }

    public function hashSensitiveData(array $data, string $table): array
    {
        $hashedData = $data;
        $sensitiveFields = $this->sensitiveFields[$table] ?? [];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $hashedData[$field] = hash('sha256', $data[$field]);
            }
        }

        return $hashedData;
    }

    public function generateEncryptionKey(): string
    {
        return base64_encode(random_bytes(32));
    }

    public function rotateEncryptionKey(string $newKey): bool
    {
        try {
            // Store old key for re-encryption
            $oldKey = $this->encryptionKey;
            
            // Update configuration
            Config::set('app.key', $newKey);
            
            Log::info('Encryption key rotated', [
                'timestamp' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to rotate encryption key', [
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    public function reEncryptData(string $table, string $oldKey, string $newKey): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $encryptedFields = $this->encryptedFields[$table] ?? [];
            
            if (empty($encryptedFields)) {
                return $results;
            }

            // This is a simplified approach - in production, use batch processing
            $records = \DB::table($table)->get();
            
            foreach ($records as $record) {
                $recordData = (array) $record;
                $updated = false;
                
                foreach ($encryptedFields as $field) {
                    if (!empty($recordData[$field])) {
                        try {
                            // Decrypt with old key
                            $decryptedValue = $this->decryptWithKey($recordData[$field], $oldKey);
                            
                            // Encrypt with new key
                            $encryptedValue = $this->encryptWithKey($decryptedValue, $newKey);
                            
                            // Update record
                            \DB::table($table)
                                ->where('id', $record->id)
                                ->update([$field => $encryptedValue]);
                            
                            $updated = true;
                        } catch (\Exception $e) {
                            $results['failed']++;
                            $results['errors'][] = "Record {$record->id}, Field {$field}: {$e->getMessage()}";
                        }
                    }
                }
                
                if ($updated) {
                    $results['success']++;
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to re-encrypt data', [
                'table' => $table,
                'error' => $e->getMessage(),
            ]);
            
            $results['errors'][] = "General error: {$e->getMessage()}";
        }

        return $results;
    }

    private function encryptWithKey(string $value, string $key): string
    {
        $cipher = $this->encryptionCipher;
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($value, $cipher, base64_decode($key), 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    private function decryptWithKey(string $encryptedValue, string $key): string
    {
        $cipher = $this->encryptionCipher;
        $data = base64_decode($encryptedValue);
        $ivLength = openssl_cipher_iv_length($cipher);
        
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        
        return openssl_decrypt($encrypted, $cipher, base64_decode($key), 0, $iv);
    }

    public function getEncryptionStatistics(): array
    {
        return [
            'encrypted_tables' => array_keys($this->encryptedFields),
            'sensitive_tables' => array_keys($this->sensitiveFields),
            'total_encrypted_fields' => array_sum(array_map('count', $this->encryptedFields)),
            'total_sensitive_fields' => array_sum(array_map('count', $this->sensitiveFields)),
            'encryption_cipher' => $this->encryptionCipher,
        ];
    }

    public function validateEncryption(): array
    {
        $issues = [];

        // Test encryption/decryption
        try {
            $testValue = 'test_encryption_value';
            $encrypted = $this->encryptField('test', 'test_field', $testValue);
            $decrypted = $this->decryptField('test', 'test_field', $encrypted);
            
            if ($decrypted !== $testValue) {
                $issues[] = 'Encryption/decryption test failed';
            }
        } catch (\Exception $e) {
            $issues[] = "Encryption test error: {$e->getMessage()}";
        }

        // Check encryption key
        if (empty($this->encryptionKey)) {
            $issues[] = 'Encryption key is not set';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
        ];
    }

    public function addEncryptedField(string $table, string $field): void
    {
        if (!isset($this->encryptedFields[$table])) {
            $this->encryptedFields[$table] = [];
        }

        if (!in_array($field, $this->encryptedFields[$table])) {
            $this->encryptedFields[$table][] = $field;
        }
    }

    public function addSensitiveField(string $table, string $field): void
    {
        if (!isset($this->sensitiveFields[$table])) {
            $this->sensitiveFields[$table] = [];
        }

        if (!in_array($field, $this->sensitiveFields[$table])) {
            $this->sensitiveFields[$table][] = $field;
        }
    }
}
