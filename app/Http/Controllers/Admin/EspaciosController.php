<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use App\Models\Imagen;
use Illuminate\Http\Request;

class EspaciosController extends Controller
{
    /**
     * Lista todos los espacios con sus estadísticas.
     */
    public function index()
    {
        // Estadísticas
        $total_espacios = Espacio::count();
        $total_espacios_activos = Espacio::where('esp_estado', 'Activo')->count();
        $total_espacios_inactivos = Espacio::where('esp_estado', 'Inactivo')->count();

        // Listado de espacios
        $espacios = Espacio::all();

        return view('admin.espacios.index', compact(
            'total_espacios',
            'total_espacios_activos',
            'total_espacios_inactivos',
            'espacios'
        ));
    }

    /**
     * Alterna el estado de un espacio entre Activo e Inactivo.
     */
    public function toggleStatus($id)
    {
        $espacio = Espacio::findOrFail($id);

        // Lógica IF(esp_estado = 'Activo', 'Inactivo', 'Activo')
        $espacio->esp_estado = ($espacio->esp_estado === 'Activo') ? 'Inactivo' : 'Activo';
        $espacio->save();

        return back()->with('status', 'Estado del espacio "' . $espacio->esp_nombre . '" actualizado a ' . $espacio->esp_estado);
    }

    /**
     * Muestra el formulario para crear un espacio.
     */
    public function create()
    {
        return view('admin.espacios.create');
    }

    /**
     * Guarda un nuevo espacio en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'esp_id'        => 'required|unique:espacios,espacio_id',
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'required|string',
            'capacidad'     => 'required|integer|min:1',
            'tipo_oficina'  => 'required|string',
            'Precio_hora'   => 'required|numeric',
            'foto'          => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'esp_id.unique' => 'El ID del espacio ya existe. Usa otro.',
        ]);

        // 1. Crear el espacio
        $espacio = Espacio::create([
            'espacio_id'      => $request->esp_id,
            'esp_nombre'      => $request->nombre,
            'esp_descripcion' => $request->descripcion,
            'esp_capacidad'   => $request->capacidad,
            'esp_tipo'        => $request->tipo_oficina,
            'esp_precio_hora' => $request->Precio_hora,
            'esp_estado'      => 'Activo',
        ]);

        // 2. Manejar la imagen
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nombreArchivo = time() . "_" . $file->getClientOriginalName();
            
            // Usamos public_path para mantener consistencia con el sistema antiguo si es necesario
            // o simplemente el disco public de Laravel. 
            // Para seguir la lógica del usuario:
            $file->move(public_path('uploads'), $nombreArchivo);
            $ruta = "" . $nombreArchivo;

            Imagen::create([
                'espacio_id' => $espacio->espacio_id,
                'foto'       => $ruta
            ]);
        }

        return redirect()->route('admin.espacios.index')
            ->with('status', '✔️ Espacio registrado correctamente');
    }

    /**
     * Muestra el formulario para editar un espacio.
     */
    public function edit($id)
    {
        $espacio = Espacio::findOrFail($id);
        return view('admin.espacios.edit', compact('espacio'));
    }

    /**
     * Procesa la actualización de un espacio.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'capacidad'    => 'required|integer|min:1',
            'tipo_oficina' => 'required|string',
            'Precio_hora'  => 'required|numeric',
        ]);

        $espacio = Espacio::findOrFail($id);
        
        $espacio->update([
            'esp_nombre'      => $request->nombre,
            'esp_descripcion' => $request->descripcion,
            'esp_capacidad'   => $request->capacidad,
            'esp_tipo'        => $request->tipo_oficina,
            'esp_precio_hora' => $request->Precio_hora,
        ]);

        return redirect()->route('admin.espacios.index')
            ->with('status', '✔️ Espacio actualizado correctamente');
    }
}
