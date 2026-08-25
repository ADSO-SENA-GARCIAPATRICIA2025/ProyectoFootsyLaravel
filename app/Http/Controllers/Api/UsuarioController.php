<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    // GET /api/usuarios
    public function index()
    {
        $usuarios = User::all();

        return response()->json($usuarios);
    }

    // GET /api/usuarios/{id}
    public function show($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json($usuario);
    }

    // POST /api/usuarios
    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'telefono' => 'nullable|string|max:30',
            'fechaNacimiento' => 'nullable|date',
            'rolUsuario' => 'required|in:cliente,admin',
            'genero' => 'nullable|in:mujer,hombre,otro,prefiero_no_decirlo',
            'estadoActivo' => 'required|boolean',
        ]);

        $usuario = User::create($datos);

        return response()->json([
            'mensaje' => 'Usuario creado correctamente',
            'usuario' => $usuario,
        ], 201);
    }

    // PUT /api/usuarios/{id}
    public function update(Request $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado'
            ], 404);
        }

        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'telefono' => 'nullable|string|max:30',
            'fechaNacimiento' => 'nullable|date',
            'rolUsuario' => 'required|in:cliente,admin',
            'genero' => 'nullable|in:mujer,hombre,otro,prefiero_no_decirlo',
            'estadoActivo' => 'required|boolean',
        ]);

        // Si no mandamos nueva contraseña, no la modificamos.
        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return response()->json([
            'mensaje' => 'Usuario actualizado correctamente',
            'usuario' => $usuario,
        ]);
    }

    // DELETE /api/usuarios/{id}
    public function destroy($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado'
            ], 404);
        }

        $usuario->delete();

        return response()->json([
            'mensaje' => 'Usuario eliminado correctamente'
        ]);
    }
}
