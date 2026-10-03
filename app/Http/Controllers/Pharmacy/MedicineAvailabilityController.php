<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Services\Pharmacy\Contracts\MedicationStockProviderInterface;
use App\Services\Pharmacy\DTOs\MedicationStockData;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * MedicineAvailabilityController — provides read-only stock availability, batch/lot, and expiry monitoring for Pharmacy.
 */
class MedicineAvailabilityController extends Controller
{
    public function __construct(
        protected MedicationStockProviderInterface $stockProvider
    ) {}

    public function index(Request $request): View
    {
        $providerError = false;
        $errorMessage = null;
        $allStockItems = collect();

        try {
            $rawStock = $this->stockProvider->getAllMedicationStock();
            $allStockItems = collect($rawStock);
        } catch (\Throwable $e) {
            Log::error('Pharmacy stock provider failure: ' . $e->getMessage(), ['exception' => $e]);
            $providerError = true;
            $errorMessage = 'Medicine availability is currently unavailable. Please try again later.';
        }

        $todayTs = strtotime(date('Y-m-d'));
        $expiringThresholdTs = strtotime(date('Y-m-d', strtotime('+90 days')));

        // Map items with derived status fields
        $processedItems = $allStockItems->map(function (MedicationStockData $item) use ($todayTs, $expiringThresholdTs) {
            $qty = $item->availableQuantity;
            
            // Stock Status
            if ($qty > 20) {
                $stockStatusKey = 'in_stock';
                $stockStatusLabel = 'In Stock';
                $stockBadgeClass = 'bg-success bg-opacity-10 text-success';
            } elseif ($qty >= 1 && $qty <= 20) {
                $stockStatusKey = 'low';
                $stockStatusLabel = 'Low Stock';
                $stockBadgeClass = 'bg-warning bg-opacity-10 text-warning-emphasis';
            } else {
                $stockStatusKey = 'out';
                $stockStatusLabel = 'Out of Stock';
                $stockBadgeClass = 'bg-danger bg-opacity-10 text-danger';
            }

            // Expiration Status
            $expTs = !empty($item->expiryDate) ? strtotime($item->expiryDate) : null;
            if (!$expTs || $expTs <= $todayTs) {
                $expiryStatusKey = 'expired';
                $expiryStatusLabel = 'Expired';
                $expiryBadgeClass = 'bg-danger bg-opacity-10 text-danger';
            } elseif ($expTs <= $expiringThresholdTs) {
                $expiryStatusKey = 'expiring_soon';
                $expiryStatusLabel = 'Expiring Soon';
                $expiryBadgeClass = 'bg-warning bg-opacity-10 text-warning-emphasis';
            } else {
                $expiryStatusKey = 'unexpired';
                $expiryStatusLabel = 'Unexpired';
                $expiryBadgeClass = 'bg-success bg-opacity-10 text-success';
            }

            return [
                'dto'                 => $item,
                'medication_name'     => $item->medicationName,
                'available_quantity'  => $qty,
                'lot_number'          => $item->lotNumber,
                'expiry_date'         => $item->expiryDate,
                'is_available'        => $item->isAvailable,
                'error_message'       => $item->errorMessage,
                'stock_status_key'    => $stockStatusKey,
                'stock_status_label'  => $stockStatusLabel,
                'stock_badge_class'   => $stockBadgeClass,
                'expiry_status_key'   => $expiryStatusKey,
                'expiry_status_label' => $expiryStatusLabel,
                'expiry_badge_class'  => $expiryBadgeClass,
            ];
        });

        // Summary Statistics (calculated from all provider records)
        $stats = [
            'total_medicines'    => $processedItems->count(),
            'in_stock'           => $processedItems->where('stock_status_key', 'in_stock')->where('expiry_status_key', '!=', 'expired')->count(),
            'low_stock'          => $processedItems->where('stock_status_key', 'low')->count(),
            'attention_required' => $processedItems->filter(function ($item) {
                return $item['stock_status_key'] === 'out' 
                    || $item['expiry_status_key'] === 'expiring_soon' 
                    || $item['expiry_status_key'] === 'expired';
            })->count(),
        ];

        // Apply Search & Filters
        $filteredItems = $processedItems;

        // Search filter (Medicine name or Lot number)
        if ($search = trim((string)$request->input('search'))) {
            $filteredItems = $filteredItems->filter(function ($item) use ($search) {
                return stripos($item['medication_name'], $search) !== false
                    || stripos($item['lot_number'], $search) !== false;
            });
        }

        // Stock status filter
        if ($stockFilter = $request->input('stock')) {
            if ($stockFilter !== 'all') {
                $filteredItems = $filteredItems->where('stock_status_key', $stockFilter);
            }
        }

        // Expiration status filter
        if ($expiryFilter = $request->input('expiry')) {
            if ($expiryFilter !== 'all') {
                $filteredItems = $filteredItems->where('expiry_status_key', $expiryFilter);
            }
        }

        return view('pharmacy.medicines.index', [
            'medicines'     => $filteredItems->values(),
            'stats'         => $stats,
            'providerError' => $providerError,
            'errorMessage'  => $errorMessage,
        ]);
    }
}
