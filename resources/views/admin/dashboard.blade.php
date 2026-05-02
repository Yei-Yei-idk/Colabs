@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <h2>Dashboard</h2>
    <p>Resumen de reservas y estadísticas generales.</p>

    {{-- ===== ESTADÍSTICAS RÁPIDAS ===== --}}
    <div class="stats">
        <div class="card">
            {{ $reservas }}
            <small>Reservas</small>
        </div>
        <div class="card">
            {{ $espaciosDisponibles }}
            <small>Espacios disponibles</small>
        </div>
        <div class="card">
            {{ $solicitudesPendientes }}
            <small>Solicitudes pendientes</small>
        </div>
    </div>

    {{-- ===== CONFIGURACIÓN DEL SITIO ===== --}}
    <div style="margin-top: 32px; background: #fff; border-radius: 14px; padding: 1.5rem 2rem; box-shadow: 0 2px 12px rgba(0,0,0,0.07); border: 1px solid #f0f0f0;">
        <h3 style="margin: 0 0 0.25rem; font-size: 1rem; color: #1a1a2e; font-weight: 700;">⚙️ Configuración del sitio</h3>
        <p style="margin: 0 0 1.25rem; color: #6b7280; font-size: 0.875rem;">Controla qué secciones son visibles para los clientes en el menú de navegación.</p>

        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 1rem 1.25rem; border-radius: 10px; border: 1.5px solid {{ $promocionesVisible ? '#d1fae5' : '#fee2e2' }}; background: {{ $promocionesVisible ? '#f0fdf4' : '#fff5f5' }}; transition: all .3s;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div style="font-size: 1.6rem;">🎁</div>
                <div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: #1a1a2e;">Sección de Promociones</div>
                    <div style="font-size: 0.8rem; color: #6b7280; margin-top: 2px;">
                        Enlace "Promociones" en el menú de navegación del cliente
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 1rem;">
                {{-- Badge de estado --}}
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700;
                    background: {{ $promocionesVisible ? '#dcfce7' : '#fee2e2' }};
                    color: {{ $promocionesVisible ? '#15803d' : '#b91c1c' }};">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $promocionesVisible ? '#22c55e' : '#ef4444' }}; display: inline-block;"></span>
                    {{ $promocionesVisible ? 'ACTIVO' : 'DESACTIVADO' }}
                </span>

                {{-- Botón toggle --}}
                <form action="{{ route('admin.toggle_promociones') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.1rem;
                               border-radius: 8px; border: none; cursor: pointer; font-size: 0.88rem; font-weight: 700;
                               transition: all .2s;
                               background: {{ $promocionesVisible ? '#ef4444' : '#22c55e' }};
                               color: #fff;"
                        onmouseover="this.style.opacity='.85'"
                        onmouseout="this.style.opacity='1'">
                        @if($promocionesVisible)
                            🚫 Deshabilitar
                        @else
                            ✅ Habilitar
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== ÚLTIMAS RESERVAS ===== --}}
    <div class="latest-reservas" style="margin-top:20px;">
        <h3>Últimas reservas</h3>

        <table style="width:100%;border-collapse:collapse;box-shadow:0 2px 8px rgba(0,0,0,0.05);border-radius:8px;overflow:hidden;">
            <thead style="background:#f9fafb;text-align:left;">
                <tr>
                    <th>Cliente</th>
                    <th>Espacio</th>
                    <th>Hora inicio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimasReservas as $reserva)
                    <tr>
                        <td>{{ $reserva->usuario->user_nombre ?? 'N/D' }}</td>
                        <td>{{ $reserva->espacio->esp_nombre ?? 'N/D' }}</td>
                        <td>{{ $reserva->rsva_hora_inicio }}</td>
                        <td>{{ $reserva->rsva_estado }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:1rem;">No hay reservas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

