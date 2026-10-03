<?php

namespace App\Services\Pharmacy\Contracts;

use App\Models\PrescriptionItem;
use App\Services\Pharmacy\DTOs\MedicationStockData;

/**
 * Interface contract defining PMS integration boundary with an external Inventory / Stock System.
 */
interface MedicationStockProviderInterface
{
    /**
     * Retrieve stock information for a single prescription item.
     */
    public function getMedicationStock(PrescriptionItem $item): MedicationStockData;

    /**
     * Retrieve stock information for multiple prescription items in batch.
     *
     * @param iterable<PrescriptionItem> $items
     * @return array<int, MedicationStockData> Indexed by prescription_item_id
     */
    public function batchGetMedicationStock(iterable $items): array;

    /**
     * Retrieve stock information for all monitored medications in the inventory system.
     *
     * @return array<int, MedicationStockData>
     */
    public function getAllMedicationStock(): array;
}

