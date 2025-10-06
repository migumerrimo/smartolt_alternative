<?php

namespace App\Http\Controllers;

use App\Models\ChangeHistory;
use App\Models\User;
use App\Models\Olt;
use Illuminate\Http\Request;

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
