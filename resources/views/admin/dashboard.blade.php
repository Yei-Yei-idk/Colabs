@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- ===== KPI CARDS ===== --}}
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="kpi-info">
                <div class="kpi-value">{{ $reservasActivasHoy }}</div>
                <div class="kpi-label">Reservas activas hoy</div>
            </div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div class="kpi-info">
                <div class="kpi-value">${{ number_format($ingresosDelMes, 0, ',', '.') }}</div>
                <div class="kpi-label">Ingresos estimados del mes</div>
            </div>
        </div>
        <div class="kpi-card purple">
            <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div class="kpi-info">
                <div class="kpi-value">{{ $usuariosRegistrados }}</div>
                <div class="kpi-label">Usuarios registrados</div>
            </div>
        </div>
        <div class="kpi-card orange">
            <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/><path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/></svg></div>
            <div class="kpi-info">
                <div class="kpi-value">{{ $solicitudesPendientes }}</div>
                <div class="kpi-label">Solicitudes pendientes</div>
            </div>
        </div>
    </div>

    {{-- ===== GANTT DEL DÍA ===== --}}
    <div class="gantt-wrapper">
        <div class="gantt-header">
            <h3><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Timeline de reservas — Hoy ({{ now()->translatedFormat('d \d\e F Y') }})</h3>
            <small>{{ $reservasDelDia->count() }} reserva(s) programada(s)</small>
        </div>

        @if($reservasDelDia->isEmpty())
            <div class="gantt-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> No hay reservas programadas para hoy.
            </div>
        @else
            @php
                $horaInicio = 7;
                $horaFin    = 22;
                $totalCols  = $horaFin - $horaInicio;
                // Agrupar por espacio
                $porEspacio = $reservasDelDia->groupBy('espacio_id');
            @endphp
            <table class="gantt-table">
                <thead>
                    <tr>
                        <th class="th-espacio">Espacio</th>
                        @for($h = $horaInicio; $h <= $horaFin; $h++)
                            <th>{{ sprintf('%02d:00', $h) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($porEspacio as $espacioId => $rsvs)
                        @php $espacio = $rsvs->first()->espacio; @endphp
                        <tr>
                            <td title="{{ $espacio->esp_nombre ?? 'N/D' }}">
                                {{ Str::limit($espacio->esp_nombre ?? 'N/D', 18) }}
                            </td>
                            <td colspan="{{ $totalCols + 1 }}" class="gantt-td">
                                <div class="gantt-cell gantt-cell-full">
                                    @foreach($rsvs as $r)
                                        @php
                                            $start = (int) explode(':', $r->rsva_hora_inicio)[0] + (int) explode(':', $r->rsva_hora_inicio)[1] / 60;
                                            $end   = (int) explode(':', $r->rsva_hora_fin)[0] + (int) explode(':', $r->rsva_hora_fin)[1] / 60;
                                            $left  = max(0, ($start - $horaInicio) / $totalCols * 100);
                                            $width = max(1, ($end - $start) / $totalCols * 100);
                                            $clase = strtolower($r->rsva_estado);
                                        @endphp
                                        <div class="gantt-bar {{ $clase }}"
                                             style="left:{{ $left }}%;width:{{ $width }}%;"
                                             title="{{ $r->usuario->user_nombre ?? '' }} | {{ $r->rsva_hora_inicio }} – {{ $r->rsva_hora_fin }} | {{ $r->rsva_estado }}">
                                            {{ $r->usuario->first_name ?? '' }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===== CONFIGURACIÓN DEL SITIO ===== --}}
    <div class="section-card">
        <h3><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Configuración del sitio</h3>
        <p class="settings-text">Controla qué secciones son visibles para los clientes en el menú de navegación.</p>

        <div class="toggle-container {{ $promocionesVisible ? 'active' : 'inactive' }}">
            <div class="toggle-left">
                <div class="toggle-icon"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg></div>
                <div>
                    <div class="toggle-info-title">Sección de Promociones</div>
                    <div class="toggle-info-desc">Enlace "Promociones" en el menú de navegación del cliente</div>
                </div>
            </div>
            <div class="toggle-right">
                <span class="toggle-badge {{ $promocionesVisible ? 'active' : 'inactive' }}">
                    <span class="toggle-dot {{ $promocionesVisible ? 'active' : 'inactive' }}"></span>
                    {{ $promocionesVisible ? 'ACTIVO' : 'DESACTIVADO' }}
                </span>
                <form action="{{ route('admin.toggle_promociones') }}" method="POST">
                    @csrf
                    <button type="submit" class="toggle-btn {{ $promocionesVisible ? 'active' : 'inactive' }}">
                        @if($promocionesVisible) <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg> Deshabilitar @else <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Habilitar @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== ÚLTIMAS RESERVAS ===== --}}
    <div class="section-card">
        <h3><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Últimas reservas</h3>
        <table class="reservas-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Espacio</th>
                    <th>Fecha</th>
                    <th>Hora inicio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimasReservas as $reserva)
                    <tr>
                        <td>{{ $reserva->usuario->user_nombre ?? 'N/D' }}</td>
                        <td>{{ $reserva->espacio->esp_nombre ?? 'N/D' }}</td>
                        <td>{{ \Carbon\Carbon::parse($reserva->rsva_fecha)->format('d/m/Y') }}</td>
                        <td>{{ $reserva->rsva_hora_inicio }}</td>
                        <td>
                            @php $estado = strtolower($reserva->rsva_estado); @endphp
                            <span class="badge badge-{{ $estado }}">
                                {{ $reserva->rsva_estado }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-table">No hay reservas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
