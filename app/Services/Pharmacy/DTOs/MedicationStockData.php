<?php

namespace App\Services\Pharmacy\DTOs;

/**
 * Data Transfer Object representing medication stock information from an external inventory provider.
 */
class MedicationStockData
{
    public function __construct(
        public string $medicationName,
        public int $availableQuantity,
        public string $lotNumber,
        public string $expiryDate,
        public bool $isAvailable = true,
        public ?string $errorMessage = null
    ) {}

    /**
     * Check if requested quantity is available in stock.
     */
    public function hasSufficientStock(int $requestedQuantity): bool
    {
        return $this->isAvailable && $this->availableQuantity >= $requestedQuantity;
    }

    /**
     * Check if medication batch is unexpired.
     */
    public function isUnexpired(): bool
    {
        if (empty($this->expiryDate)) {
            return false;
        }

        return strtotime($this->expiryDate) > strtotime(date('Y-m-d'));
    }

    /**
     * Array representation for views and logs.
     */
    public function toArray(): array
    {
        return [
            'medication_name'    => $this->medicationName,
            'available_quantity' => $this->availableQuantity,
            'lot_number'         => $this->lotNumber,
            'expiry_date'        => $this->expiryDate,
            'is_available'       => $this->isAvailable,
            'error_message'      => $this->errorMessage,
        ];
    }
}
