<?php

namespace App\Http\Controllers;

use App\Models\ChangeHistory;
use App\Models\User;
use App\Models\Olt;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ChangeHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Iniciamos la consulta con relaciones
        $query = ChangeHistory::with(['user', 'olt']);

        // 🔍 Filtro por búsqueda general
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_name', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%")
                  ->orWhere('command', 'LIKE', "%$search%")
                  ->orWhere('result', 'LIKE', "%$search%");
            });
        }

        // ⚙️ Filtro por tipo de dispositivo
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        // 👤 Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 📋 Obtenemos todos los usuarios para el combobox
        $users = User::all();

        // Obtenemos los registros de BD (sin paginar aún)
        $dbChanges = $query->orderByDesc('date')->get();

        // Construimos entradas desde fichero JSONL siempre, para mezclar con BD
        $fileItems = [];
        $path = storage_path('logs/change_history.log');
        if (file_exists($path)) {
            $lines = array_filter(array_map('trim', file($path)));
            // leemos las últimas 300 (invertimos para tener más recientes primero)
            $lines = array_reverse($lines);
            $lines = array_slice($lines, 0, 300);

            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if (!$data) continue;

                // Aplicamos filtros básicos también al log de fichero
                if ($request->filled('device_type') && ($data['device_type'] ?? '') !== $request->device_type) {
                    continue;
                }
                if ($request->filled('user_id')) {
                    $uid = $data['user_id'] ?? null;
                    if ((string)$uid !== (string)$request->user_id) {
                        continue;
                    }
                }
                if ($request->filled('search')) {
                    $hay = strtolower($request->search);
                    $haystack = strtolower(($data['device_name'] ?? '') . ' ' . ($data['description'] ?? '') . ' ' . ($data['command'] ?? '') . ' ' . ($data['result'] ?? ''));
                    if (strpos($haystack, $hay) === false) {
                        continue;
                    }
                }

                $userObj = (object) ['name' => 'Sistema'];
                if (!empty($data['user_id'])) {
                    $userModel = User::find($data['user_id']);
                    $userObj = (object) ['name' => $userModel ? $userModel->name : ('Usuario ' . $data['user_id'])];
                }

                $item = (object)[];
                $item->id = null;
                $item->user = $userObj;
                $item->device_name = $data['device_name'] ?? null;
                $item->device_type = $data['device_type'] ?? null;
                $item->entity_type = $data['entity_type'] ?? null;
                $item->description = $data['description'] ?? null;
                $item->command = $data['command'] ?? null;
                $item->result = $data['result'] ?? null;
                $item->date = isset($data['date']) ? Carbon::parse($data['date']) : Carbon::now();
                $item->source = 'file';
                $item->user_id = $data['user_id'] ?? null;
                $fileItems[] = $item;
            }
        }

        // Normalizamos registros de BD a incluir campo 'source'
        $dbChanges->each(function ($c) {
            $c->source = 'db';
        });

        // Mezclamos BD + fichero y ordenamos por fecha desc
        $merged = collect($dbChanges)->merge($fileItems)->sortByDesc(function ($item) {
            return $item->date instanceof Carbon ? $item->date : Carbon::parse($item->date);
        })->values();

        // Paginación manual del merge
        $perPage = 20;
        $page = $request->get('page', 1);
        $total = $merged->count();
        $sliced = $merged->slice(($page - 1) * $perPage, $perPage)->all();
        $changes = new LengthAwarePaginator($sliced, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query()
        ]);

        // Si se pide JSON, devolvemos los datos en formato API
        if ($request->wantsJson()) {
            return response()->json($changes);
        }

        // 🔹 Retornamos la vista principal con los datos
        return view('change_history.index', compact('changes', 'users'));
    }

    public function create()
    {
        $users = User::all();
        $olts = Olt::all();

        return view('change_history.create', compact('users','olts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'olt_id'      => 'required|exists:olts,id',
            'device_type' => 'required|in:OLT,ONU,ROUTER,SWITCH,SERVER',
            'device_name' => 'nullable|string|max:100',
            'command'     => 'nullable|string',
            'result'      => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $change = ChangeHistory::create($validated);

        if ($request->wantsJson()) {
            return response()->json($change, 201);
        }

        return redirect()->route('change-history.index')
                         ->with('success', 'Cambio registrado correctamente');
    }

    public function show(Request $request, ChangeHistory $changeHistory)
    {
        if ($request->wantsJson()) {
            return response()->json($changeHistory->load(['user','olt']));
        }

        return view('change_history.show', compact('changeHistory'));
    }

    public function edit(ChangeHistory $changeHistory)
    {
        $users = User::all();
        $olts = Olt::all();

        return view('change_history.edit', compact('changeHistory','users','olts'));
    }

    public function update(Request $request, ChangeHistory $changeHistory)
    {
        $validated = $request->validate([
            'device_name' => 'nullable|string|max:100',
            'command'     => 'nullable|string',
            'result'      => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $changeHistory->update($validated);

        if ($request->wantsJson()) {
            return response()->json($changeHistory);
        }

        return redirect()->route('change-history.index')
                         ->with('success', 'Cambio actualizado correctamente');
    }

    public function destroy(Request $request, ChangeHistory $changeHistory)
    {
        $changeHistory->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('change-history.index')
                         ->with('success', 'Cambio eliminado correctamente');
    }
}
