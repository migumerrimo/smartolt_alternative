<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Olt;
use App\Services\OltSshService;

class OltSshApiController extends Controller
{
    public function listVlans($id)
    {
        $olt = Olt::findOrFail($id);

        $ssh = new OltSshService(
            $olt->management_ip,
            $olt->ssh_port,
            $olt->ssh_username,
            $olt->ssh_password
        );

        $response = $ssh->getVlans();

        return response()->json($response);
    }

    public function alarms($id)
    {
        $olt = Olt::findOrFail($id);

        $ssh = new OltSshService(
            $olt->management_ip,
            $olt->ssh_port,
            $olt->ssh_username,
            $olt->ssh_password
        );

        $response = $ssh->getAlarms();

        return response()->json($response);
    }

    public function onus($id)
    {
        $olt = Olt::findOrFail($id);

        $ssh = new OltSshService(
            $olt->management_ip,
            $olt->ssh_port,
            $olt->ssh_username,
            $olt->ssh_password
        );

        $response = $ssh->listOnus();

        return response()->json($response);
    }

    public function system($id)
    {
        $olt = Olt::findOrFail($id);

        $ssh = new OltSshService(
            $olt->management_ip,
            $olt->ssh_port,
            $olt->ssh_username,
            $olt->ssh_password
        );

        $response = $ssh->getSystem();

        return response()->json($response);
    }
}

