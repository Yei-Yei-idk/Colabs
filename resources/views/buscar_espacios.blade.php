@extends('layouts.app')

@section('title', 'Buscar Espacios - COLABS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/publico/buscar-espacios.css') }}">
@endpush

@section('content')
<section id="buscar" class="section active">
    <div class="public-search-header">
        <h2>Buscar Espacios</h2>
        <p>Explora espacios disponibles y filtra segun tu necesidad.</p>
    </div>

    <div class="buscar-container">
        <aside class="sidebar">
            <form method="GET" action="{{ route('buscar_espacios') }}">
                <h3>Filtrar Espacios</h3>

                <div class="filtro">
                    <label for="tipo">Tipo de espacio:</label>
                    <select name="esp_tipo" id="tipo">
                        <option value="">Todos</option>
                        <option value="Oficina" {{ $tipo == 'Oficina' ? 'selected' : '' }}>Oficina</option>
                        <option value="Sala de reuniones" {{ $tipo == 'Sala de reuniones' ? 'selected' : '' }}>Sala de reuniones</option>
                        <option value="Sala de eventos" {{ $tipo == 'Sala de eventos' ? 'selected' : '' }}>Sala de eventos</option>
                        <option value="Aula" {{ $tipo == 'Aula' ? 'selected' : '' }}>Aula</option>
                    </select>
                </div>

                <div class="filtro">
                    <label for="capacidad">Capacidad maxima:</label>
                    <select name="esp_capacidad" id="capacidad">
                        <option value="">Todas</option>
                        <option value="5" {{ $capacidad == '5' ? 'selected' : '' }}>Hasta 5 personas</option>
                        <option value="10" {{ $capacidad == '10' ? 'selected' : '' }}>Hasta 10 personas</option>
                        <option value="15" {{ $capacidad == '15' ? 'selected' : '' }}>Hasta 15 personas</option>
                        <option value="20" {{ $capacidad == '20' ? 'selected' : '' }}>Hasta 20 personas</option>
                    </select>
                </div>

                <div class="filtro">
                    <label for="precio">Precio maximo por hora:</label>
                    <select name="esp_precio_hora" id="precio">
                        <option value="">Sin limite</option>
                        <option value="20000" {{ $precioMax == '20000' ? 'selected' : '' }}>Hasta $20.000</option>
                        <option value="50000" {{ $precioMax == '50000' ? 'selected' : '' }}>Hasta $50.000</option>
                        <option value="100000" {{ $precioMax == '100000' ? 'selected' : '' }}>Hasta $100.000</option>
                        <option value="200000" {{ $precioMax == '200000' ? 'selected' : '' }}>Hasta $200.000</option>
                    </select>
                </div>

                <button type="submit" class="btn-sidebar-apply">Aplicar filtros</button>
            </form>
        </aside>

        <div class="espacios-listado">
            @forelse ($espacios as $espacio)
                @php
                    $imgSrc = $espacio->imagen ? $espacio->imagen->foto : 'OF1 .jpeg';
                @endphp
                <div class="espacio-card">
                    <img src="{{ asset('uploads/' . $imgSrc) }}"
                         alt="{{ $espacio->esp_nombre }}"
                         data-fallback="{{ asset('uploads/OF1 .jpeg') }}"
                         onerror="this.src=this.getAttribute('data-fallback')">

                    <div class="espacio-info">
                        <h3>{{ $espacio->esp_nombre }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($espacio->esp_descripcion, 100) }}</p>
                        <p><strong>Capacidad:</strong> {{ $espacio->esp_capacidad }} personas</p>
                        <p><strong>Tipo:</strong> {{ $espacio->esp_tipo }}</p>
                    </div>

                    <div class="reserva-actions-column">
                        @auth
                            <a href="{{ route('cliente.reservar', $espacio->espacio_id) }}" class="btn-reservar">
                                Reservar ahora ->
                            </a>
                        @else
                            <a href="{{ route('login', ['redirect' => route('cliente.reservar', ['id' => $espacio->espacio_id], false)]) }}" class="btn-reservar">
                                Inicia sesion para reservar
                            </a>
                        @endauth
                    </div>
                </div>
            @empty
                <p>No hay espacios que coincidan con los filtros seleccionados.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
