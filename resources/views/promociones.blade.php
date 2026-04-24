@extends('layouts.cliente')

@section('title', 'Promociones por Paquetes de Horas - Colabs')

@section('content')
 <br>
 <br>
 <br>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones por Paquetes de Horas - Colabs</title>
    <!-- Tailwind CSS para el diseño -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
                Paquetes de <span class="text-yellow-500">Horas</span>
            </h1>
            <p class="mt-4 text-xl text-gray-600">Ahorra seleccionando un paquete de horas para cualquier espacio.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Paquete 4 Horas -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="inline-flex items-center justify-center p-3 bg-yellow-100 rounded-xl mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Paquete Básico</h2>
                <div class="flex justify-center items-baseline my-4">
                    <span class="text-5xl font-extrabold text-gray-900">4</span>
                    <span class="text-xl text-gray-500 ml-1">Horas</span>
                </div>
                <p class="text-3xl text-emerald-600 font-extrabold mb-8">10% <span class="text-xl font-medium text-gray-400">OFF</span></p>
                <a href="{{ route('cliente.buscar_espacios', ['paquete' => 4]) }}" class="block w-full py-3 px-4 bg-gray-900 text-yellow-400 font-bold rounded-xl hover:bg-black transition-colors duration-200">
                    Seleccionar Espacio
                </a>
            </div>

            <!-- Paquete 5 Horas (Destacado) -->
            <div class="bg-gray-900 rounded-2xl shadow-xl border-2 border-yellow-500 p-8 text-center relative transform md:-translate-y-4 hover:-translate-y-6 transition-all duration-300">
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <span class="bg-yellow-500 text-black text-sm font-bold uppercase tracking-wider py-1 px-4 rounded-full">
                        Más Popular
                    </span>
                </div>
                <div class="inline-flex items-center justify-center p-3 bg-gray-800 rounded-xl mb-4 mt-2">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Paquete Medio</h2>
                <div class="flex justify-center items-baseline my-4">
                    <span class="text-5xl font-extrabold text-white">5</span>
                    <span class="text-xl text-gray-400 ml-1">Horas</span>
                </div>
                <p class="text-3xl text-yellow-400 font-extrabold mb-8">15% <span class="text-xl font-medium text-gray-500">OFF</span></p>
                <a href="{{ route('cliente.buscar_espacios', ['paquete' => 5]) }}" class="block w-full py-3 px-4 bg-yellow-500 text-black font-bold rounded-xl shadow-md hover:bg-yellow-400 hover:shadow-lg transition-all duration-200">
                    Seleccionar Espacio
                </a>
            </div>

            <!-- Paquete 6 Horas -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="inline-flex items-center justify-center p-3 bg-yellow-100 rounded-xl mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Paquete Premium</h2>
                <div class="flex justify-center items-baseline my-4">
                    <span class="text-5xl font-extrabold text-gray-900">6</span>
                    <span class="text-xl text-gray-500 ml-1">Horas</span>
                </div>
                <p class="text-3xl text-emerald-600 font-extrabold mb-8">20% <span class="text-xl font-medium text-gray-400">OFF</span></p>
                <a href="{{ route('cliente.buscar_espacios', ['paquete' => 6]) }}" class="block w-full py-3 px-4 bg-gray-900 text-yellow-400 font-bold rounded-xl hover:bg-black transition-colors duration-200">
                    Seleccionar Espacio
                </a>
            </div>
        </div>
        
  
    </div>
</body>
</html>
