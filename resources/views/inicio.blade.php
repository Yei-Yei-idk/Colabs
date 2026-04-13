{{-- resources/views/inicio.blade.php --}}
@extends('layouts.app')

@section('title', 'Colabs — Espacios de trabajo')

@section('content')

{{-- ===== SECCIÓN INICIO ===== --}}
<section id="inicio" class="section active">

    {{-- Card: ¿Qué es Colabs? --}}
    <div class="container hero-main-container">
        <h2 class="hero-title">¿Qué es Colabs?</h2>
        <p class="hero-subtitle">
            Somos una empresa de alquiler de espacios —salones, oficinas y más—
            pensada para que otras empresas nunca se queden sin zonas de trabajo.
            Contamos con cientos de espacios, la mayoría ya amoblados, listos para
            que tu equipo empiece a trabajar desde el primer día.
        </p>
    </div>

    {{-- CARD DE BÚSQUEDA INDEPENDIENTE --}}
    <div class="search-card-wrapper">
        <div class="search-card">
            <div class="search-card-header">
                <h3>Encuentra tu espacio hoy</h3>
                <p>Selecciona el tipo de oficina y la capacidad que necesitas.</p>
            </div>

            <form action="{{ route('buscar_espacios') }}" method="GET" class="search-form">
                <div class="search-input-group">
                    <select name="esp_tipo">
                        <option value="">¿Qué tipo de espacio buscas?</option>
                        <option value="Oficina">Oficinas Privadas</option>
                        <option value="Sala de reuniones">Sala de reuniones</option>
                        <option value="Sala de eventos">Sala de eventos</option>
                        <option value="Aula">Aulas / Salones</option>
                    </select>
                </div>
                <div class="search-input-group">
                    <select name="esp_capacidad">
                        <option value="">Capacidad</option>
                        <option value="5">Hasta 5 personas</option>
                        <option value="10">Hasta 10 personas</option>
                        <option value="20">Hasta 20 personas</option>
                    </select>
                </div>
                <button type="submit" class="btn-search-submit">
                    Buscar espacios
                </button>
            </form>
            
            {{-- Decoración sutil --}}
            <div class="availability-badge">DISPONIBILIDAD INMEDIATA</div>
        </div>
    </div>

    {{-- Grid de espacios --}}
    <section class="espacios">
        <h2>Conoce nuestros espacios &rsaquo;&rsaquo;</h2>

        @foreach ($espacios as $espacio)
            <div class="espacio {{ $espacio['invertido'] ? 'invertido' : '' }}">

                @unless ($espacio['invertido'])
                    <img
                        src="{{ asset('ASSETS/' . $espacio['imagen']) }}"
                        alt="{{ $espacio['alt'] }}"
                        loading="lazy"
                        width="440" height="300"
                    >
                @endunless

                <div class="texto">
                    <h3>| {{ $espacio['titulo'] }} |</h3>
                    <p>{{ $espacio['desc'] }}</p>
                    <a href="{{ $espacio['link'] }}">Ver más</a>
                </div>

                @if ($espacio['invertido'])
                    <img
                        src="{{ asset('ASSETS/' . $espacio['imagen']) }}"
                        alt="{{ $espacio['alt'] }}"
                        loading="lazy"
                        width="440" height="300"
                    >
                @endif

            </div>
        @endforeach

    </section>

</section>
@endsection
