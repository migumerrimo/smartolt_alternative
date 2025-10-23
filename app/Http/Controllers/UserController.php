<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PDF;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->wantsJson()) return response()->json($users);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required|string|max:100',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6',
            'role'=>'required|in:admin,technician,support,customer,read-only',
            'phone'=>'nullable|string|max:20',
            'active'=>'boolean',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        if ($request->wantsJson()) return response()->json($user,201);
        return redirect()->route('users.index')->with('success','Usuario creado');
    }

    public function show(Request $request, User $user)
    {
        if ($request->wantsJson()) return response()->json($user);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'=>'required|string|max:100',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'password'=>'nullable|string|min:6',
            'role'=>'required|in:admin,technician,support,customer,read-only',
            'phone'=>'nullable|string|max:20',
            'active'=>'boolean',
        ]);
        if(isset($validated['password'])) $validated['password'] = bcrypt($validated['password']);
        $user->update($validated);
        if ($request->wantsJson()) return response()->json($user);
        return redirect()->route('users.index')->with('success','Usuario actualizado');
    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();
        if ($request->wantsJson()) return response()->json(null,204);
        return redirect()->route('users.index')->with('success','Usuario eliminado');
    }

     /**
     * Generar PDF con listado de usuarios
     */
    public function generatePDF()
    {
        $users = User::all();
        
        $data = [
            'users' => $users,
            'title' => 'Reporte de Usuarios',
            'date' => now()->format('d/m/Y H:i:s'),
            'totalUsers' => $users->count(),
            'activeUsers' => $users->where('active', true)->count(),
            'adminUsers' => $users->where('role', 'admin')->count(),
            'technicianUsers' => $users->where('role', 'technician')->count(),
        ];

        $pdf = PDF::loadView('users.pdf', $data);
        
        return $pdf->download('reporte-usuarios-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Vista previa del PDF (HTML)
     */
    public function previewPDF()
    {
        $users = User::all();
        
        $data = [
            'users' => $users,
            'title' => 'Reporte de Usuarios',
            'date' => now()->format('d/m/Y H:i:s'),
            'totalUsers' => $users->count(),
            'activeUsers' => $users->where('active', true)->count(),
            'adminUsers' => $users->where('role', 'admin')->count(),
            'technicianUsers' => $users->where('role', 'technician')->count(),
        ];

        return view('users.pdf', $data);
    }
}
