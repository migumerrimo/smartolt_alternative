<?php

namespace App\Http\Controllers;

use App\Models\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterRequestController extends Controller
{
    /**
     * Store a newly created register request (PUBLIC ACCESS)
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email|unique:register_requests,email',
                'phone' => 'nullable|string|max:20',
                'role' => 'required|in:technician,support,customer,read-only',
                'notes' => 'required|string|min:10|max:500',
            ]);

            // Crear la solicitud
            RegisterRequest::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'requested_role' => $validated['role'],
                'notes' => $validated['notes'],
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada correctamente. Un administrador la revisará pronto.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = array_map(function($messages) {
                return is_array($messages) ? implode(', ', $messages) : $messages;
            }, $e->errors());
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(' ', $errors)
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en solicitud de registro: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la solicitud. Por favor intenta nuevamente.'
            ], 500);
        }
    }

    /**
     * Display a listing of register requests (ADMIN ONLY)
     */
    public function index()
    {
        // Simple authorization check - puedes mejorar esto después con Policies
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $requests = RegisterRequest::with('processor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.register-requests', compact('requests'));
    }

    /**
     * Approve a register request (ADMIN ONLY)
     */
    public function approve(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        DB::transaction(function () use ($request, $id) {
            $registerRequest = RegisterRequest::findOrFail($id);

            // Verificar que no esté ya procesada
            if (!$registerRequest->isPending()) {
                throw new \Exception('Esta solicitud ya fue procesada.');
            }

            // Crear usuario
            $user = User::create([
                'name' => $registerRequest->name,
                'email' => $registerRequest->email,
                'phone' => $registerRequest->phone,
                'role' => $registerRequest->requested_role,
                'password' => Hash::make('temp_password_' . rand(1000, 9999)), // Password temporal
                'active' => true,
            ]);

            // Marcar solicitud como aprobada
            $registerRequest->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
        });

        return redirect()->route('admin.register-requests.index')
            ->with('success', 'Solicitud aprobada y usuario creado exitosamente.');
    }

    /**
     * Reject a register request (ADMIN ONLY)
     */
    public function reject(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        $registerRequest = RegisterRequest::findOrFail($id);

        // Verificar que no esté ya procesada
        if (!$registerRequest->isPending()) {
            return redirect()->back()->with('error', 'Esta solicitud ya fue procesada.');
        }

        $registerRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.register-requests.index')
            ->with('success', 'Solicitud rechazada exitosamente.');
    }
}