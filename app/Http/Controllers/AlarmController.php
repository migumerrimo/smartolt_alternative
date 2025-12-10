<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Olt;
use App\Models\Onu;
use App\Services\OltSshService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AlarmController extends Controller
{
    public function index(Request $request)
    {
        // Sincroniza alertas activas desde la OLT antes de consultar la BD
        $this->refreshActiveAlarmsFromOlt();

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

    /**
     * Obtiene alarmas activas directamente desde la OLT y las refleja en la BD.
     * Se usa una sola conexión SSH de solo lectura para no comprometer el servicio.
     */
    protected function refreshActiveAlarmsFromOlt(): void
    {
        $olt = Olt::where('ssh_active', true)->first();

        if (!$olt) {
            return; // No hay OLT habilitada para SSH
        }

        try {
            $port = $olt->ssh_port ?: 22;
            $ssh = new OltSshService($olt->management_ip, $port, $olt->ssh_username, $olt->ssh_password);
            // Historial completo para capturar fault y recovery (cleared)
            $result = $ssh->getAlarmHistory();

            if (($result['status'] ?? '') !== 'success') {
                return;
            }

            $parsed = $this->parseHuaweiAlarms($result['raw'], $olt->id);

            foreach ($parsed as $alarmData) {
                // Evita duplicados usando combinación de mensaje + timestamp
                Alarm::updateOrCreate(
                    [
                        'olt_id' => $alarmData['olt_id'],
                        'onu_id' => $alarmData['onu_id'],
                        'message' => $alarmData['message'],
                        'detected_at' => $alarmData['detected_at'],
                    ],
                    [
                        'severity' => $alarmData['severity'],
                        'active' => $alarmData['active'],
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudieron sincronizar las alarmas OLT: '.$e->getMessage());
        }
    }

    /**
     * Intenta extraer severidad, mensaje y hora de la salida Huawei.
     * El formato de MA5680T suele incluir severidades (Critical/Major/Minor/Warning/Info)
     * y timestamps; si no se encuentra fecha, se usa el momento actual.
     */
    protected function parseHuaweiAlarms(string $rawOutput, int $oltId): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $rawOutput);
        $alarms = [];
        $current = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            // Encabezado de alarma
            if (preg_match('/^ALARM\s+(\d+)\s+(FAULT|RECOVERY(?:\s+CLEARED)?|CLEAR|CLEARED)\s+([A-Z]+)\s+\S+\s+.*?(20\d{2}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}(?:[\+\-]\d{2}:\d{2})?)/i', $trimmed, $m)) {
                if ($current) {
                    $alarms[] = $this->finalizeHuaweiAlarm($current);
                }

                $statusToken = strtoupper($m[2]);
                $severityToken = strtolower($m[3]);
                $detectedAt = now();
                try {
                    $detectedAt = Carbon::parse($m[4], config('app.timezone'));
                } catch (\Throwable $e) {
                    // deja la hora actual si falla el parseo
                }

                $current = [
                    'olt_id' => $oltId,
                    'onu_id' => null,
                    'active' => str_contains($statusToken, 'FAULT'),
                    'severity' => match ($severityToken) {
                        'critical', 'crit' => 'critical',
                        'major', 'maj' => 'major',
                        'minor', 'min' => 'minor',
                        'warning', 'warn', 'wrn' => 'warning',
                        default => 'info'
                    },
                    'alarm_name' => null,
                    'description' => null,
                    'cause' => null,
                    'advice' => null,
                    'detected_at' => $detectedAt,
                ];
                continue;
            }

            if (!$current) {
                continue; // ignora líneas previas a un bloque de alarma
            }

            if (stripos($trimmed, 'ALARM NAME') === 0 && str_contains($trimmed, ':')) {
                $current['alarm_name'] = trim(substr($trimmed, strpos($trimmed, ':') + 1));
            } elseif (stripos($trimmed, 'DESCRIPTION') === 0 && str_contains($trimmed, ':')) {
                $current['description'] = trim(substr($trimmed, strpos($trimmed, ':') + 1));
            } elseif (stripos($trimmed, 'CAUSE') === 0 && str_contains($trimmed, ':')) {
                $current['cause'] = trim(substr($trimmed, strpos($trimmed, ':') + 1));
            } elseif (stripos($trimmed, 'ADVICE') === 0 && str_contains($trimmed, ':')) {
                $current['advice'] = trim(substr($trimmed, strpos($trimmed, ':') + 1));
            } elseif (stripos($trimmed, '--- END') === 0) {
                $alarms[] = $this->finalizeHuaweiAlarm($current);
                $current = null;
            }
        }

        if ($current) {
            $alarms[] = $this->finalizeHuaweiAlarm($current);
        }

        return $alarms;
    }

    /**
     * Compone el mensaje consolidado de la alarma Huawei.
     */
    protected function finalizeHuaweiAlarm(array $alarm): array
    {
        $parts = [];
        if (!empty($alarm['alarm_name'])) {
            $parts[] = $alarm['alarm_name'];
        }
        if (!empty($alarm['description'])) {
            $parts[] = $alarm['description'];
        }
        if (!empty($alarm['advice'])) {
            $parts[] = 'Advice: '.$alarm['advice'];
        }
        if (!empty($alarm['cause'])) {
            $parts[] = 'Cause: '.$alarm['cause'];
        }

        $message = trim(implode(' | ', array_filter($parts)));
        if ($message === '') {
            $message = 'Alarma sin descripción (Huawei history)';
        }

        return [
            'olt_id' => $alarm['olt_id'],
            'onu_id' => $alarm['onu_id'],
            'severity' => $alarm['severity'],
            'message' => $message,
            'active' => $alarm['active'],
            'detected_at' => $alarm['detected_at'],
        ];
    }
}