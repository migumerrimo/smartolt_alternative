<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\OltSSHService;
use Illuminate\Support\Facades\Session;

class ConnectToOltOnLogin
{
    public function handle(Login $event)
    {
        $ssh = app(OltSSHService::class)->connect();

        // Guardamos la instancia serializada de la sesión SSH
        Session::put('olt_ssh_connected', true);
        Session::put('olt_ssh', $ssh);
    }
}

