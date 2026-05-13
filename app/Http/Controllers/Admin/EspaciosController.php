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
        $total_espacios = Espacio::query()->count('*');
        $total_espacios_activos = Espacio::query()->where('esp_estado', '=', 'Activo')->count('*');
        $total_espacios_inactivos = Espacio::query()->where('esp_estado', '=', 'Inactivo')->count('*');

        // Listado de espacios
        $espacios = Espacio::query()->get();

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
    public function toggleStatus(string $id)
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
            'espacio_id'      => $request->input('esp_id'),
            'esp_nombre'      => $request->input('nombre'),
            'esp_descripcion' => $request->input('descripcion'),
            'esp_capacidad'   => $request->input('capacidad'),
            'esp_tipo'        => $request->input('tipo_oficina'),
            'esp_precio_hora' => $request->input('Precio_hora'),
            'esp_estado'      => 'Activo',
        ]);

        // 2. Manejar la imagen
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            // Nombre único: evita colisiones aunque se suba el mismo archivo varias veces
            $nombreArchivo = uniqid('', true) . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads'), $nombreArchivo);

            Imagen::create([
                'espacio_id' => $espacio->espacio_id,
                'foto'       => $nombreArchivo,
            ]);
        }

        return redirect()->route('admin.espacios.index')
            ->with('status', '✔️ Espacio registrado correctamente');
    }

    /**
     * Muestra el formulario para editar un espacio.
     */
    public function edit(string $id)
    {
        $espacio = Espacio::findOrFail($id);
        return view('admin.espacios.edit', compact('espacio'));
    }

    /**
     * Procesa la actualización de un espacio.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'capacidad'    => 'required|integer|min:1',
            'tipo_oficina' => 'required|string',
            'Precio_hora'  => 'required|numeric',
            'foto'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $espacio = Espacio::findOrFail($id);

        $espacio->update([
            'esp_nombre'      => $request->input('nombre'),
            'esp_descripcion' => $request->input('descripcion'),
            'esp_capacidad'   => $request->input('capacidad'),
            'esp_tipo'        => $request->input('tipo_oficina'),
            'esp_precio_hora' => $request->input('Precio_hora'),
        ]);

        // Actualizar imagen si se subió una nueva
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            // Nombre único: evita colisiones aunque el archivo tenga el mismo nombre
            $nombreArchivo = uniqid('', true) . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $nombreArchivo);

            $imagen = Imagen::query()->where('espacio_id', '=', $espacio->espacio_id)->first();

            if ($imagen) {
                // Eliminar el archivo físico anterior si existe
                $rutaAnterior = public_path('uploads/' . $imagen->foto);
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
                // Eloquent actualiza por PK (img_id) correctamente
                $imagen->update(['foto' => $nombreArchivo]);
            } else {
                // Crear registro si el espacio nunca tuvo imagen
                Imagen::create([
                    'espacio_id' => $espacio->espacio_id,
                    'foto'       => $nombreArchivo,
                ]);
            }
        }

        return redirect()->route('admin.espacios.index')
            ->with('status', '✔️ Espacio actualizado correctamente');
    }
}
