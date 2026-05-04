<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('apellido')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'apellido'        => ['required', 'string', 'max:100', 'unique:clientes,apellido'],
            'nombre_completo' => ['required', 'string', 'max:200'],
            'celular'         => ['nullable', 'string', 'max:30'],
            'direccion'       => ['nullable', 'string', 'max:200'],
            'nro_socio'       => ['nullable', 'string', 'max:20'],
        ]);

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:200'],
            'celular'         => ['nullable', 'string', 'max:30'],
            'direccion'       => ['nullable', 'string', 'max:200'],
            'nro_socio'       => ['nullable', 'string', 'max:20'],
        ]);

        $cliente->update($data);

        return redirect()->back()->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }
}
