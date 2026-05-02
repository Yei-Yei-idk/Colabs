@extends('layouts.cliente')

@section('title', 'Mis Reservas - COLABS')

@section('content')
<div class="mis-reservas-header animate-fade-up">
    <h2>Mis Reservas</h2>
    <p class="text-muted text-center">Gestiona todas tus reservas, su estado, y su historial.</p>
</div>

@if(session('success'))
    <div class="alert alert-success mis-reservas-list">
        {{ session('success') }}
    </div>
@endif

<div class="espacios-listado mis-reservas-list">
    @forelse($reservas as $reserva)
        @php
            // Obtener imagen del espacio
            $imagen = \App\Models\Imagen::where('espacio_id', $reserva->espacio_id)->first();
            $imgSrc = $imagen ? $imagen->foto : null;
            
            // Calcular info dinámica (Horas y Costo)
            $horaInicio = \Carbon\Carbon::parse($reserva->hora_inicio);
            $horaFin = \Carbon\Carbon::parse($reserva->hora_fin);
            $diferencia_horas = $horaFin->diffInHours($horaInicio);
            $total_estimado = $diferencia_horas * $reserva->esp_precio_hora;
            $fecha_formato = \Carbon\Carbon::parse($reserva->fecha)->translatedFormat('d \d\e F, Y');

            // Detectar si tiene descuento de paquete
            $descuentoPorcentaje = 0;
            $paqueteHoras = 0;
            $tieneDescuento = false;
            if (!empty($reserva->descripcion) && str_contains($reserva->descripcion, '[INFO PAQUETE:')) {
                preg_match('/descuento del (\d+)%/', $reserva->descripcion, $matchPct);
                preg_match('/paquete de (\d+) horas/', $reserva->descripcion, $matchHrs);
                if (!empty($matchPct[1])) {
                    $descuentoPorcentaje = (int) $matchPct[1];
                    $paqueteHoras = (int) ($matchHrs[1] ?? 0);
                    $tieneDescuento = true;
                    // Calcular total con descuento
                    $total_estimado = $total_estimado * (1 - $descuentoPorcentaje / 100);
                }
            }
        @endphp
        
        <div class="reserva-card-main {{ 'border-' . strtolower($reserva->estado) }} animate-fade-up">
            <div class="reserva-id-tag">
                #{{ str_pad($reserva->reserva_id, 4, '0', STR_PAD_LEFT) }}
            </div>

            @if($imgSrc)
                <img src="{{ asset('uploads/' . $imgSrc) }}" alt="{{ $reserva->esp_nombre }}"
                     data-fallback="{{ asset('uploads/OF1 .jpeg') }}" onerror="this.src=this.getAttribute('data-fallback')">
            @else
                <img src="{{ asset('uploads/OF1 .jpeg') }}" alt="Sin imagen">
            @endif
            
            <div class="reserva-info-body">
                <h3>{{ $reserva->esp_nombre }}</h3>
                <p>{{ Str::limit($reserva->esp_descripcion, 80) }}</p>
                
                <div class="reserva-meta">
                    <div>📅 <strong>{{ $fecha_formato }}</strong></div>
                    <div>🕒 <strong>{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('h:i A') }}</strong> ({{ $diferencia_horas }}h)</div>
                </div>

                {{-- Badge de descuento --}}
                @if($tieneDescuento)
                    <div style="margin-top: 10px;">
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.78rem; font-weight: 700; border: 1px solid #6ee7b7; box-shadow: 0 1px 4px rgba(16,185,129,0.15);">
                            🎁 {{ $descuentoPorcentaje }}% OFF · Paquete {{ $paqueteHoras }}h
                        </span>
                    </div>
                @endif
            </div>

            <div class="reserva-actions-column">
                <div class="reserva-total-box">
                    <div class="reserva-total-label">
                        @if($tieneDescuento)
                            Total <span style="font-size:0.7rem; color:#059669; font-weight:700;">(con desc.)</span>:
                        @else
                            Total:
                        @endif
                    </div>
                    <div class="reserva-total-amount" style="{{ $tieneDescuento ? 'color: #059669;' : '' }}">
                        ${{ number_format($total_estimado, 0, ',', '.') }}
                    </div>
                </div>

                <div class="reserva-badge {{ 'badge-' . strtolower($reserva->estado) }}">
                    {{ strtoupper($reserva->estado) }}
                </div>

                @if($reserva->estado === 'Finalizada')
                    @php
                        $ya_calificado = \App\Models\Calificacion::where('reserva_id', $reserva->reserva_id)->exists();
                    @endphp
                    
                    @if(!$ya_calificado)
                        <button type="button" data-espacio-id="{{ $reserva->espacio_id }}" data-reserva-id="{{ $reserva->reserva_id }}" onclick="openReviewModal(this.getAttribute('data-espacio-id'), this.getAttribute('data-reserva-id'))" 
                                class="btn-reservar btn-calificar">
                            ⭐ Calificar
                        </button>
                    @endif
                @endif

                <a href="{{ route('cliente.detalles_reserva', $reserva->reserva_id) }}" class="btn-reservar mt-5">
                    Ver Detalles →
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state animate-fade-up" style="animation-delay: 0.2s;">
            <div class="empty-state-icon">🔍</div>
            <h3>¡Aún no tienes reservas!</h3>
            <p class="text-muted mb-20">Explora nuestros espacios y encuentra el lugar ideal para trabajar.</p>
            <a href="{{ route('cliente.buscar_espacios') }}" class="btn-principal">Buscar Espacios</a>
        </div>
    @endforelse
</div>
@endsection
