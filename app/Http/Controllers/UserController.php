<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::where('role', 'empleado')
                        ->orderBy('box_nombre')
                        ->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:150', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'box_nombre' => ['required', 'string', 'max:50'],
        ]);

        User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'empleado',
            'box_nombre' => $data['box_nombre'],
            'box_status' => 'libre',
        ]);

        return redirect()->route('usuarios.index')->with('success', "Usuario {$data['name']} creado.");
    }

    public function update(Request $request, User $usuario)
    {
        // Proteger: no editar el admin por esta ruta
        abort_if($usuario->isAdmin(), 403);

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario->id)],
            'box_nombre' => ['required', 'string', 'max:50'],
            'password'   => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $usuario->name       = $data['name'];
        $usuario->email      = $data['email'];
        $usuario->box_nombre = $data['box_nombre'];

        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', "Usuario {$usuario->name} actualizado.");
    }

    public function destroy(User $usuario)
    {
        abort_if($usuario->isAdmin(), 403);

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', "Usuario {$nombre} eliminado.");
    }
}
