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

        // Compatibilidad con phpseclib: definir globales NET_SSH2_* desde phpseclib3\Net\SSH2 (parche temporal)
        if (class_exists(\phpseclib3\Net\SSH2::class)) {
            $ref = new \ReflectionClass(\phpseclib3\Net\SSH2::class);
            $consts = $ref->getConstants();
            foreach ($consts as $name => $value) {
                $global = 'NET_SSH2_' . $name;
                if (!defined($global)) {
                    define($global, $value);
                }
            }
        }

        // Compatibilidad con phpseclib legacy constants (parche temporal)
        if (!defined('NET_SSH2_DISCONNECT_BY_APPLICATION')) {
            if (class_exists(\phpseclib3\Net\SSH2::class) && defined('\phpseclib3\Net\SSH2::DISCONNECT_BY_APPLICATION')) {
                // intenta definir desde la constante de la clase si existe
                define('NET_SSH2_DISCONNECT_BY_APPLICATION', \phpseclib3\Net\SSH2::DISCONNECT_BY_APPLICATION);
            } else {
                define('NET_SSH2_DISCONNECT_BY_APPLICATION', 1);
            }
        }

        // añade aquí otras constantes si el log te muestra más "Undefined constant"
    }
}
