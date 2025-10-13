<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Onu;
use App\Models\Olt;
use App\Models\ChangeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index()
    {
        // Versión temporal básica - solo lista clientes
        $customers = Customer::with(['user'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:50',
            'customer_type' => 'required|in:residential,business,corporate',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make('temp_password_123'),
            'role' => 'customer',
            'active' => true,
        ]);

        // Crear el perfil de cliente
        $customer = Customer::create([
            'user_id' => $user->id,
            'customer_type' => $validated['customer_type'],
            'address' => $validated['address'],
            'document_number' => $validated['document_number'],
            'service_start_date' => now(),
        ]);

        // Registrar en el historial
        ChangeHistory::create([
            'user_id' => auth()->id(),
            'olt_id' => 1, // OLT por defecto
            'device_type' => 'ONU',
            'device_name' => 'NUEVO_CLIENTE',
            'description' => "Cliente creado: {$user->name}",
        ]);

        return redirect()->route('customers.index')
                        ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        // Versión temporal sin asignaciones ONU
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for assigning ONU to customer.
     */
    public function showAssignOnu(Customer $customer)
    {
        $availableOnus = Onu::whereDoesntHave('customerAssignments', function($query) {
            $query->where('status', 'active');
        })->with('olt')->get();

        return view('customers.assign-onu', compact('customer', 'availableOnus'));
    }

    /**
     * Assign ONU to customer.
     */
    public function assignOnu(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'onu_id' => 'required|exists:onus,id',
            'monthly_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verificar que la ONU no esté asignada a otro cliente
        $existingAssignment = \App\Models\CustomerOnuAssignment::where('onu_id', $validated['onu_id'])
                                                                ->where('status', 'active')
                                                                ->first();

        if ($existingAssignment) {
            return back()->with('error', 'Esta ONU ya está asignada a otro cliente.');
        }

        // Crear la asignación (usaremos el modelo temporalmente)
        try {
            $assignment = \App\Models\CustomerOnuAssignment::create([
                'customer_id' => $customer->id,
                'onu_id' => $validated['onu_id'],
                'assigned_by' => auth()->id(),
                'monthly_cost' => $validated['monthly_cost'],
                'notes' => $validated['notes'],
            ]);

            // Registrar en el historial
            $onu = Onu::find($validated['onu_id']);
            ChangeHistory::create([
                'user_id' => auth()->id(),
                'olt_id' => $onu->olt_id,
                'device_type' => 'ONU',
                'device_name' => $onu->serial_number,
                'description' => "ONU asignada al cliente: {$customer->user->name}",
            ]);

            return redirect()->route('customers.show', $customer)
                            ->with('success', 'ONU asignada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar ONU: ' . $e->getMessage());
        }
    }
}