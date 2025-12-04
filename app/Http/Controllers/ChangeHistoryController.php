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

        // 📊 Ordenamos por fecha descendente y paginamos
        $changes = $query->orderByDesc('date')->paginate(20);

        // 📋 Obtenemos todos los usuarios para el combobox
        $users = User::all();

        // Si se pide JSON, devolvemos los datos en formato API
        if ($request->wantsJson()) {
            return response()->json($changes);
        }

        // Si no hay entradas en la BD, usamos el fichero JSONL como fallback
        if ($changes->total() == 0) {
            $path = storage_path('logs/change_history.log');
            $items = [];
            if (file_exists($path)) {
                $lines = array_filter(array_map('trim', file($path)));
                // leemos las últimas 100 entradas (invertimos para tener más recientes primero)
                $lines = array_reverse($lines);
                $lines = array_slice($lines, 0, 200);

                foreach ($lines as $line) {
                    $data = json_decode($line, true);
                    if (!$data) continue;
                    $user = null;
                    if (!empty($data['user_id'])) {
                        $userModel = User::find($data['user_id']);
                        $user = (object)['name' => $userModel ? $userModel->name : ('Usuario ' . $data['user_id'])];
                    } else {
                        $user = (object)['name' => 'Sistema'];
                    }

                    $item = (object)[];
                    $item->id = null;
                    $item->user = $user;
                    $item->device_name = $data['device_name'] ?? null;
                    $item->device_type = $data['device_type'] ?? null;
                    $item->entity_type = $data['entity_type'] ?? null;
                    $item->description = $data['description'] ?? null;
                    $item->command = $data['command'] ?? null;
                    $item->result = $data['result'] ?? null;
                    $item->date = isset($data['date']) ? Carbon::parse($data['date']) : Carbon::now();
                    $items[] = $item;
                }
            }

            // Paginador manual
            $perPage = 20;
            $page = $request->get('page', 1);
            $total = count($items);
            $offset = ($page - 1) * $perPage;
            $sliced = array_slice($items, $offset, $perPage);
            $paginator = new LengthAwarePaginator($sliced, $total, $perPage, $page, [
                'path' => $request->url(),
                'query' => $request->query()
            ]);

            $changes = $paginator;
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
