<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Olt;
use App\Models\Onu;
use App\Models\Vlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DataController extends Controller
{
    /**
     * 🔹 API: Obtener todas las OLTs registradas en la base de datos
     * Endpoint: GET /api/data/olts
     */
    public function getOlts(Request $request)
    {
        // Puedes agregar filtros si quieres: ?status=active
        $query = Olt::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Obtener datos ordenados por fecha de creación
        $olts = $query->orderByDesc('created_at')->get();

        // Retornar respuesta en formato JSON
        return response()->json([
            'total' => $olts->count(),
            'data' => $olts
        ]);
    }

    /**
     * 🔹 API adicional: obtener ONUs (por si quieres probar varias tablas)
     * Endpoint: GET /api/data/onus
     */
    public function getOnus()
    {
        $onus = Onu::with(['olt'])->get();
        return response()->json([
            'total' => $onus->count(),
            'data' => $onus
        ]);
    }

    /**
     * 🔹 API adicional: obtener VLANs
     * Endpoint: GET /api/data/vlans
     */
    public function getVlans()
    {
        $vlans = Vlan::with('olt')->get();
        return response()->json([
            'total' => $vlans->count(),
            'data' => $vlans
        ]);
    }

    /**
     * 🔹 API adicional: obtener Usuarios
     * Endpoint: GET /api/data/users
     */
    public function getUsers()
    {
        $users = User::select('id', 'name', 'email', 'role', 'active', 'created_at')->get();
        return response()->json([
            'total' => $users->count(),
            'data' => $users
        ]);
    }

    /**
     * GET /api/olt/ssh/status
     * Comprueba conectividad TCP hacia la OLT (primera OLT registrada o .env)
     */
    public function oltSshStatus(Request $request): JsonResponse
    {
        try {
            $olt = Olt::first();
            $host = $olt?->host ?? env('OLT_HOST', '127.0.0.1');
            $port = (int) ($olt?->port ?? env('OLT_PORT', 23));
            $timeout = 5;

            $errNo = 0;
            $errStr = '';
            $fp = @fsockopen($host, $port, $errNo, $errStr, $timeout);

            if ($fp) {
                fclose($fp);
                return response()->json([
                    'connected' => true,
                    'host' => $host,
                    'port' => $port,
                    'message' => "Conexión TCP exitosa a {$host}:{$port}"
                ], 200);
            }

            return response()->json([
                'connected' => false,
                'host' => $host,
                'port' => $port,
                'message' => "No se pudo conectar a {$host}:{$port} - {$errStr} ({$errNo})"
            ], 200);
        } catch (\Throwable $e) {
            // Logging para diagnóstico
            \Log::error('oltSshStatus error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json([
                'connected' => false,
                'message' => 'Error interno comprobando estado SSH. Revisa storage/logs/laravel.log'
            ], 500);
        }
    }
}
