<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Services\OltSshService;
use Illuminate\Http\Request;

class OltSshApiController extends Controller
{
    protected $ssh;

    public function __construct(OltSshService $ssh)
    {
        $this->ssh = $ssh;
    }

    public function system($id)
    {
        $olt = Olt::findOrFail($id);

        $ssh = $this->ssh->connect(
            $olt->management_ip,
            'root',
            'admin123'
        );

        if (!is_object($ssh)) {
            return response()->json($ssh, 500);
        }

        // Comando real de Huawei
        $output = $this->ssh->run($ssh, "display version");

        return response()->json([
            "success" => true,
            "command" => "display version",
            "response" => $output
        ]);
    }

    public function alarms($id)
    {
        $olt = Olt::findOrFail($id);

        $ssh = $this->ssh->connect(
            $olt->management_ip,
            'root',
            'admin123'
        );

        if (!is_object($ssh)) {
            return response()->json($ssh, 500);
        }

        // Comando para alarmas activas
        $output = $this->ssh->run($ssh, "display alarm active");

        return response()->json([
            "success" => true,
            "command" => "display alarm active",
            "response" => $output
        ]);
    }
}
