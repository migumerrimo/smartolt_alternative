<?php
// app/Http/Controllers/OltController.php
namespace App\Http\Controllers;

use App\Models\Olt;
use App\Services\OltSSHService;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Log;

class OltController extends Controller
{
    protected $oltSSHService;

    public function __construct(OltSSHService $oltSSHService)
    {
        $this->oltSSHService = $oltSSHService;
    }

    public function index(Request $request)
    {
        $olts = Olt::all();
        
        // Agregar estado de conexión a cada OLT
        $olts->each(function($olt) {
            $olt->connection_status = $this->checkIndividualOltConnection($olt->management_ip);
        });

        if ($request->wantsJson()) return response()->json($olts);
        return view('olts.index', compact('olts'));
    }

    public function create() { return view('olts.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'model' => 'nullable|string|max:50',
            'vendor' => 'required|in:Huawei,ZTE,FiberHome,Other',
            'management_ip' => 'required|string|max:50|unique:olts,management_ip',
            'location' => 'nullable|string|max:255',
            'firmware' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ], [
            'management_ip.unique' => '⚠️ La dirección IP ingresada ya está registrada en otra OLT.',
        ]);

        $olt = Olt::create($validated);

        if ($request->wantsJson())
            return response()->json($olt, 201);

        return redirect()->route('olts.index')
                         ->with('success', 'OLT creada correctamente');
    }

    public function show(Request $request, Olt $olt)
    {
        // Agregar información de conexión y estado en tiempo real
        $olt->connection_status = $this->checkIndividualOltConnection($olt->management_ip);
        $olt->real_time_info = $this->getOltRealTimeInfo($olt);

        if ($request->wantsJson()) return response()->json($olt);
        return view('olts.show', compact('olt'));
    }

    public function edit(Olt $olt) { return view('olts.edit', compact('olt')); }

    public function update(Request $request, Olt $olt)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'model' => 'nullable|string|max:50',
            'vendor' => 'required|in:Huawei,ZTE,FiberHome,Other',
            'management_ip' => 'required|string|max:50|unique:olts,management_ip,' . $olt->id,
            'location' => 'nullable|string|max:255',
            'firmware' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ], [
            'management_ip.unique' => '⚠️ La dirección IP ingresada ya está registrada en otra OLT.',
        ]);

        $olt->update($validated);

        if ($request->wantsJson())
            return response()->json($olt);

        return redirect()->route('olts.index')
                         ->with('success', 'OLT actualizada correctamente');
    }

    public function destroy(Request $request, Olt $olt)
    {
        $olt->delete();
        if ($request->wantsJson()) return response()->json(null,204);
        return redirect()->route('olts.index')->with('success','OLT eliminada');
    }

    /**
     * Verificar conexión con OLT específica
     */
    public function checkConnection(Olt $olt)
    {
        try {
            // Configurar temporalmente la OLT específica
            config(['OLT_HOST' => $olt->management_ip]);
            
            $isConnected = $this->oltSSHService->connect();
            
            $data = [
                'connected' => $isConnected,
                'olt_id' => $olt->id,
                'olt_name' => $olt->name,
                'management_ip' => $olt->management_ip,
                'message' => $isConnected ? '✅ Conexión exitosa con OLT' : '❌ No se pudo conectar a la OLT'
            ];

            if ($isConnected) {
                // Obtener información básica si está conectada
                $data['info'] = $this->getBasicOltInfo();
            }

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error("Error verificando conexión OLT {$olt->id}: " . $e->getMessage());
            
            return response()->json([
                'connected' => false,
                'olt_id' => $olt->id,
                'message' => '❌ Error al verificar conexión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ejecutar comando en OLT específica
     */
    public function executeCommand(Request $request, Olt $olt)
    {
        $request->validate([
            'command' => 'required|string|max:255'
        ]);

        try {
            // Configurar OLT específica
            config(['OLT_HOST' => $olt->management_ip]);
            
            $output = $this->oltSSHService->safeExec($request->command, 'No se pudo ejecutar el comando');
            
            return response()->json([
                'success' => true,
                'command' => $request->command,
                'output' => $output,
                'olt_name' => $olt->name
            ]);

        } catch (\Exception $e) {
            Log::error("Error ejecutando comando en OLT {$olt->id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error ejecutando comando: ' . $e->getMessage(),
                'output' => ''
            ], 500);
        }
    }

    /**
     * Obtener información en tiempo real de la OLT
     */
    private function getOltRealTimeInfo(Olt $olt)
    {
        try {
            config(['OLT_HOST' => $olt->management_ip]);
            
            if (!$this->oltSSHService->connect()) {
                return [
                    'connected' => false,
                    'message' => 'OLT no disponible'
                ];
            }

            // Comandos básicos según el vendor
            $commands = $this->getVendorCommands($olt->vendor);
            
            $info = ['connected' => true];
            
            foreach ($commands as $key => $command) {
                $info[$key] = $this->oltSSHService->safeExec($command, 'No disponible');
            }

            return $info;

        } catch (\Exception $e) {
            return [
                'connected' => false,
                'message' => 'Error obteniendo información: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Comandos específicos por vendor
     */
    private function getVendorCommands($vendor)
    {
        $commands = [
            'ZTE' => [
                'version' => 'show version',
                'ont_summary' => 'show ont summary',
                'system_info' => 'show system information'
            ],
            'Huawei' => [
                'version' => 'display version',
                'ont_summary' => 'display ont info summary',
                'system_info' => 'display device information'
            ],
            'FiberHome' => [
                'version' => 'show version',
                'ont_summary' => 'show gpon onu state',
                'system_info' => 'show system'
            ],
            'Other' => [
                'version' => 'show version',
                'ont_summary' => 'show ont summary',
                'system_info' => 'show system information'
            ]
        ];

        return $commands[$vendor] ?? $commands['Other'];
    }

    /**
     * Verificar conexión individual sin afectar el servicio principal
     */
    private function checkIndividualOltConnection($ip)
    {
        try {
            // Crear una instancia temporal para no interferir con la conexión principal
            $tempSSHService = new OltSSHService();
            
            // Configurar temporalmente
            config(['OLT_HOST' => $ip]);
            
            return $tempSSHService->connect();
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Información básica de OLT conectada
     */
    private function getBasicOltInfo()
    {
        try {
            return [
                'version' => $this->oltSSHService->safeExec('show version', 'No disponible'),
                'uptime' => $this->oltSSHService->safeExec('show system uptime', 'No disponible'),
                'ont_count' => $this->oltSSHService->safeExec('show ont count', 'No disponible')
            ];
        } catch (\Exception $e) {
            return ['error' => 'No se pudo obtener información'];
        }
    }

    /**
     * Generar PDF con listado de OLTs
     */
    public function generatePDF()
    {
        $olts = Olt::withCount(['onus as online_onus_count' => function($query) {
                    $query->where('status', 'online');
                }])
                ->withCount('onus as total_onus_count')
                ->get();

        $data = [
            'olts' => $olts,
            'title' => 'Reporte de OLTs',
            'date' => now()->format('d/m/Y H:i:s'),
            'totalOlts' => $olts->count(),
            'activeOlts' => $olts->where('status', 'active')->count(),
            'totalOnus' => $olts->sum('total_onus_count'),
            'onlineOnus' => $olts->sum('online_onus_count'),
        ];

        $pdf = PDF::loadView('olts.pdf', $data);
        
        return $pdf->download('reporte-olts-' . now()->format('Y-m-d') . '.pdf');
    }

    public function previewPDF()
    {
        $olts = Olt::withCount(['onus as online_onus_count' => function($query) {
                    $query->where('status', 'online');
                }])
                ->withCount('onus as total_onus_count')
                ->get();

        $data = [
            'olts' => $olts,
            'title' => 'Reporte de OLTs',
            'date' => now()->format('d/m/Y H:i:s'),
            'totalOlts' => $olts->count(),
            'activeOlts' => $olts->where('status', 'active')->count(),
            'totalOnus' => $olts->sum('total_onus_count'),
            'onlineOnus' => $olts->sum('online_onus_count'),
        ];

        return view('olts.pdf', $data);
    }
}