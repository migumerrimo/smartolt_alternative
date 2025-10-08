<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Olt;
use App\Models\Onu;
use App\Models\Vlan;
use App\Models\User;

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
}
