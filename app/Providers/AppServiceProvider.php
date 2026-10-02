<?php

namespace App\Providers;

use App\Models\Patient;
use App\Policies\PatientPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Pharmacy\Contracts\MedicationStockProviderInterface::class,
            function ($app) {
                $provider = config('pharmacy.stock_provider', 'mock');
                return match ($provider) {
                    'mock' => $app->make(\App\Services\Pharmacy\MockMedicationStockProvider::class),
                    default => $app->make(\App\Services\Pharmacy\MockMedicationStockProvider::class),
                };
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Patient::class, PatientPolicy::class);
        Paginator::useBootstrapFive();
    }
}

