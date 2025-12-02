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
}
