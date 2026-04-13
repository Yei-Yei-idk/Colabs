<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function index()
    {
        $espacios = [
            [
                'titulo' => 'OFICINAS PERSONALES',
                'desc' => 'Espacio para una persona, comodo.',
                'imagen' => 'OF12.jpeg',
                'alt' => 'Oficinas personales',
                'link' => route('buscar_espacios'),
                'invertido' => false,
            ],
            [
                'titulo' => 'OFICINAS COMPARTIDAS',
                'desc' => 'Sala mas amplia para varios trabajadores.',
                'imagen' => 'Of 14 puestos de trabajo .jpeg',
                'alt' => 'Oficinas compartidas',
                'link' => route('buscar_espacios'),
                'invertido' => true,
            ],
            [
                'titulo' => 'SALAS DE REUNIONES',
                'desc' => 'Espacios modernos con todos los servicios.',
                'imagen' => 'ofic 11.jpeg',
                'alt' => 'Salas de reuniones',
                'link' => route('buscar_espacios'),
                'invertido' => false,
            ],
            [
                'titulo' => 'CAFETERIA',
                'desc' => 'Todos los trabajadores merecen un descanso.',
                'imagen' => 'WhatsApp Image 2025-09-05 at 11.24.18 AM.jpeg',
                'alt' => 'Cafeteria',
                'link' => route('buscar_espacios'),
                'invertido' => true,
            ],
        ];

        return view('inicio', compact('espacios'));
    }

    public function buscarEspaciosPublico(Request $request)
    {
        $tipo = $request->input('esp_tipo');
        $capacidad = $request->input('esp_capacidad');
        $precioMax = $request->input('esp_precio_hora');

        $query = Espacio::with('imagen')->where('esp_estado', 'Activo');

        if (!empty($tipo)) {
            $query->where('esp_tipo', $tipo);
        }

        if (!empty($capacidad)) {
            $query->where('esp_capacidad', '>=', (int) $capacidad);
        }

        if (!empty($precioMax)) {
            $query->where('esp_precio_hora', '<=', (int) $precioMax);
        }

        $espacios = $query->get();

        return view('buscar_espacios', compact('espacios', 'tipo', 'capacidad', 'precioMax'));
    }

    public function nosotros()
    {
        return view('nosotros');
    }

    public function ubicacion()
    {
        return view('ubicacion');
    }

    public function servicios()
    {
        return view('servicios');
    }
}
