<?php

namespace App\Services\Pharmacy;

use App\Models\PrescriptionItem;
use App\Services\Pharmacy\Contracts\MedicationStockProviderInterface;
use App\Services\Pharmacy\DTOs\MedicationStockData;

/**
 * MockMedicationStockProvider — Provides realistic mock medication stock, lot, and expiry data for PMS development.
 */
class MockMedicationStockProvider implements MedicationStockProviderInterface
{
    /** @var array<int|string, MedicationStockData> Custom overrides for unit/feature tests */
    protected static array $overrides = [];

    /** @var array<int|string, string> Error overrides for unit/feature tests */
    protected static array $errorOverrides = [];

    /**
     * Set explicit mock stock override for a specific medication name or item ID (for testing).
     */
    public static function setMockStock(string|int $key, MedicationStockData $stockData): void
    {
        self::$overrides[$key] = $stockData;
    }

    /**
     * Set explicit stock error override for a specific medication name or item ID (for testing).
     */
    public static function setMockError(string|int $key, string $errorMessage): void
    {
        self::$errorOverrides[$key] = $errorMessage;
    }

    /**
     * Reset test overrides.
     */
    public static function reset(): void
    {
        self::$overrides = [];
        self::$errorOverrides = [];
    }

    public function getMedicationStock(PrescriptionItem $item): MedicationStockData
    {
        // 1. Check for test error override
        if (isset(self::$errorOverrides[$item->id])) {
            return new MedicationStockData(
                medicationName: $item->medication_name,
                availableQuantity: 0,
                lotNumber: '',
                expiryDate: '',
                isAvailable: false,
                errorMessage: self::$errorOverrides[$item->id]
            );
        }

        if (isset(self::$errorOverrides[$item->medication_name])) {
            return new MedicationStockData(
                medicationName: $item->medication_name,
                availableQuantity: 0,
                lotNumber: '',
                expiryDate: '',
                isAvailable: false,
                errorMessage: self::$errorOverrides[$item->medication_name]
            );
        }

        // 2. Check for test stock override
        if (isset(self::$overrides[$item->id])) {
            return self::$overrides[$item->id];
        }

        if (isset(self::$overrides[$item->medication_name])) {
            return self::$overrides[$item->medication_name];
        }

        // 3. Return deterministic realistic mock data based on medication name
        $name = trim($item->medication_name);

        if (stripos($name, 'Paracetamol') !== false) {
            return new MedicationStockData(
                medicationName: $item->medication_name,
                availableQuantity: 100,
                lotNumber: 'PARA-2026-001',
                expiryDate: '2027-12-31',
                isAvailable: true
            );
        }

        if (stripos($name, 'Solmoux') !== false) {
            return new MedicationStockData(
                medicationName: $item->medication_name,
                availableQuantity: 50,
                lotNumber: 'SOL-2026-015',
                expiryDate: '2027-08-31',
                isAvailable: true
            );
        }

        if (stripos($name, 'Alaxan') !== false) {
            return new MedicationStockData(
                medicationName: $item->medication_name,
                availableQuantity: 75,
                lotNumber: 'ALA-2026-008',
                expiryDate: '2028-03-31',
                isAvailable: true
            );
        }

        // Default fallback mock stock for arbitrary medication names
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4)) ?: 'MED';
        $itemHash = abs(crc32($name . $item->id)) % 800 + 100;

        return new MedicationStockData(
            medicationName: $item->medication_name,
            availableQuantity: 100,
            lotNumber: "{$prefix}-2026-{$itemHash}",
            expiryDate: date('Y-m-d', strtotime('+18 months')),
            isAvailable: true
        );
    }

    public function batchGetMedicationStock(iterable $items): array
    {
        $results = [];
        foreach ($items as $item) {
            $results[$item->id] = $this->getMedicationStock($item);
        }
        return $results;
    }
}
