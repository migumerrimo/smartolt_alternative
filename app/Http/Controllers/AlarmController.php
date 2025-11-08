<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Olt;
use App\Models\Onu;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    public function index(Request $request)
    {
        // Query base - solo alertas activas
        $query = Alarm::with(['olt','onu'])
                    ->where('active', true);

        //  FILTRO POR BÚSQUEDA DE TEXTO
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('message', 'LIKE', "%{$searchTerm}%")
                ->orWhereHas('olt', function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('management_ip', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('onu', function($q) use ($searchTerm) {
                    $q->where('serial_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('model', 'LIKE', "%{$searchTerm}%");
                });
            });
        }

        // FILTRO POR SEVERIDAD
        if ($request->has('severity') && $request->severity != 'all') {
            $query->where('severity', $request->severity);
        }

        //  FILTRO POR TIPO DE DISPOSITIVO
        if ($request->has('device_type') && $request->device_type != 'all') {
            if ($request->device_type == 'olt') {
                $query->whereNull('onu_id');
            } elseif ($request->device_type == 'onu') {
                $query->whereNotNull('onu_id');
            }
        }

        //  FILTRO POR FECHA
        if ($request->has('time_filter')) {
            switch ($request->time_filter) {
                case '1h':
                    $query->where('detected_at', '>=', now()->subHour());
                    break;
                case '6h':
                    $query->where('detected_at', '>=', now()->subHours(6));
                    break;
                case '24h':
                    $query->where('detected_at', '>=', now()->subDay());
                    break;
                case '7d':
                    $query->where('detected_at', '>=', now()->subDays(7));
                    break;
            }
        }

        // Ordenar por fecha de detección (más recientes primero)
        $alarms = $query->orderBy('detected_at', 'desc')->get();

        // Contadores por severidad (para las tarjetas)
        $critical_count = Alarm::where('severity', 'critical')->where('active', true)->count();
        $major_count = Alarm::where('severity', 'major')->where('active', true)->count();
        $minor_count = Alarm::where('severity', 'minor')->where('active', true)->count();
        $warning_count = Alarm::where('severity', 'warning')->where('active', true)->count();
        $info_count = Alarm::where('severity', 'info')->where('active', true)->count();

        // Pasar parámetros de filtro a la vista
        $filters = $request->only(['search', 'severity', 'device_type', 'time_filter']);

        if ($request->wantsJson()) {
            return response()->json([
                'alarms' => $alarms,
                'counts' => [
                    'critical' => $critical_count,
                    'major' => $major_count,
                    'minor' => $minor_count,
                    'warning' => $warning_count,
                    'info' => $info_count
                ],
                'filters' => $filters
            ]);
        }

        return view('alarms.index', compact(
            'alarms', 
            'critical_count', 
            'major_count', 
            'minor_count',
            'warning_count', 
            'info_count',
            'filters'
        ));
    }

    public function create()
    {
        $olts = Olt::all();
        $onus = Onu::all();

        return view('alarms.create', compact('olts','onus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'  => 'required|exists:olts,id',
            'onu_id'  => 'nullable|exists:onus,id',
            'severity'=> 'required|in:critical,major,minor,warning,info',
            'message' => 'required|string',
            'active'  => 'boolean',
        ]);

        // Agregar timestamp de detección si no viene
        if (!isset($validated['detected_at'])) {
            $validated['detected_at'] = now();
        }

        $alarm = Alarm::create($validated);

        if ($request->wantsJson()) {
            return response()->json($alarm, 201);
        }

        return redirect()->route('alarms.index')
                         ->with('success', 'Alarma creada correctamente');
    }

    public function show(Request $request, Alarm $alarm)
    {
        // Cargar relaciones para mostrar información completa
        $alarm->load(['olt', 'onu']);

        if ($request->wantsJson()) {
            return response()->json($alarm);
        }

        return view('alarms.show', compact('alarm'));
    }

    public function edit(Alarm $alarm)
    {
        $olts = Olt::all();
        $onus = Onu::all();

        return view('alarms.edit', compact('alarm','olts','onus'));
    }

    public function update(Request $request, Alarm $alarm)
    {
        $validated = $request->validate([
            'olt_id'  => 'sometimes|required|exists:olts,id',
            'onu_id'  => 'nullable|exists:onus,id',
            'severity'=> 'required|in:critical,major,minor,warning,info',
            'message' => 'required|string',
            'active'  => 'boolean',
        ]);

        $alarm->update($validated);

        if ($request->wantsJson()) {
            return response()->json($alarm);
        }

        return redirect()->route('alarms.index')
                         ->with('success', 'Alarma actualizada correctamente');
    }

    public function destroy(Request $request, Alarm $alarm)
    {
        $alarm->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('alarms.index')
                         ->with('success', 'Alarma eliminada correctamente');
    }

    /**
     * Método adicional para marcar alerta como resuelta
     */
    public function resolve(Request $request, Alarm $alarm)
    {
        $alarm->update([
            'active' => false,
            'resolved_at' => now()
        ]);

        if ($request->wantsJson()) {
            return response()->json($alarm);
        }

        return redirect()->route('alarms.index')
                         ->with('success', 'Alarma marcada como resuelta');
    }

    /**
     * Método para obtener solo alertas activas (API)
     */
    public function activeAlarms(Request $request)
    {
        $alarms = Alarm::with(['olt','onu'])
                      ->where('active', true)
                      ->orderBy('detected_at', 'desc')
                      ->get();

        return response()->json($alarms);
    }
}