<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\ChangeLogger;
use App\Models\Olt;
use App\Models\Onu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Olt::created(fn($olt) => ChangeLogger::log('OLT', $olt->name, 'OLT creada automáticamente', 'INSERT', 'OK', $olt->id));
        Olt::deleted(fn($olt) => ChangeLogger::log('OLT', $olt->name, 'OLT eliminada', 'DELETE', 'OK', $olt->id));

        Onu::created(fn($onu) => ChangeLogger::log('ONU', $onu->serial_number, 'ONU registrada', 'INSERT', 'OK', $onu->olt_id));
        Onu::deleted(fn($onu) => ChangeLogger::log('ONU', $onu->serial_number, 'ONU eliminada', 'DELETE', 'OK', $onu->olt_id));
    }
}
