<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\ChangeLogger;
use App\Models\Olt;
use App\Models\Onu;
use App\Models\Vlan;
use App\Models\ServiceProfile;
use App\Models\DbaProfile;
use App\Models\LineProfile;
use App\Models\ServicePort;
use App\Models\Telemetry;
use App\Models\DeviceConfig;
use App\Models\Alarm;
use App\Models\Customer;
use App\Models\TrafficTable;
use App\Models\User;

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

        $models = [
            Vlan::class => 'VLAN',
            ServiceProfile::class => 'SERVICE_PROFILE',
            DbaProfile::class => 'DBA_PROFILE',
            LineProfile::class => 'LINE_PROFILE',
            ServicePort::class => 'SERVICE_PORT',
            Telemetry::class => 'TELEMETRY',
            DeviceConfig::class => 'DEVICE_CONFIG',
            Alarm::class => 'ALARM',
            Customer::class => 'CUSTOMER',
            TrafficTable::class => 'TRAFFIC_TABLE',
            User::class => 'USER'
        ];

        foreach ($models as $modelClass => $label) {
            $modelClass::created(function ($m) use ($label) {
                $name = $m->name ?? ($m->number ?? ($m->serial_number ?? ($m->id ?? '')));
                ChangeLogger::log($label, $name, "$label creado", 'INSERT', 'OK', $m->olt_id ?? null);
            });

            $modelClass::updated(function ($m) use ($label) {
                $name = $m->name ?? ($m->number ?? ($m->serial_number ?? ($m->id ?? '')));
                ChangeLogger::log($label, $name, "$label actualizado", 'UPDATE', 'OK', $m->olt_id ?? null);
            });

            $modelClass::deleted(function ($m) use ($label) {
                $name = $m->name ?? ($m->number ?? ($m->serial_number ?? ($m->id ?? '')));
                ChangeLogger::log($label, $name, "$label eliminado", 'DELETE', 'OK', $m->olt_id ?? null);
            });
        }
    }
}
