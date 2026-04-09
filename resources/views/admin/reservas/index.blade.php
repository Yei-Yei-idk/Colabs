@extends('layouts.admin')

@section('title', 'Reservas')
@section('page-title', 'Reservas')

@section('content')

<h2>Reservas</h2>
<p>Listado de reservas de clientes.</p>

{{-- ── NAVEGACIÓN DE FECHAS ────────────────────────────────── --}}
<div class="navegacion-fechas">

    <a href="{{ route('admin.reservas.index', ['fecha' => $fechaAnterior]) }}"
       class="btn-nav">⬅ Día anterior</a>

    <form method="get" action="{{ route('admin.reservas.index') }}" style="display:inline;">
        <input type="date"
               class="input-fecha"
               name="fecha"
               value="{{ $fecha->format('Y-m-d') }}"
               onchange="this.form.submit()">
    </form>

    <a href="{{ route('admin.reservas.index', ['fecha' => $fechaSiguiente]) }}"
       class="btn-nav">Día siguiente ➡</a>

</div>

{{-- ── CALENDARIO DEL DÍA ──────────────────────────────────── --}}
<h2>Calendario del día {{ $fecha->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</h2>

<div style="overflow-x:auto;">
<table class="tabla-reservas">
    <thead>
        <tr>
            <th>Hora</th>
            @foreach ($espacios as $espacio)
                <th>{{ $espacio->esp_nombre }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @php
            $slotInicio = \Carbon\Carbon::parse('06:00:00');
            $slotFin    = \Carbon\Carbon::parse('20:00:00');
        @endphp

        @while ($slotInicio < $slotFin)

            @php
                $horaActual    = $slotInicio->format('H:i:s');
                $horaSiguiente = $slotInicio->copy()->addHour()->format('H:i:s');
            @endphp

            <tr>
                <td><strong>{{ $slotInicio->format('g:i A') }}</strong></td>

                @foreach ($espacios as $espacio)
                    @php
                        $ocupado          = false;
                        $reservaEncontrada = null;

                        if ($reservas->has($espacio->espacio_id)) {
                            foreach ($reservas[$espacio->espacio_id] as $r) {
                                $rsInicio = $r->rsva_hora_inicio; // 'HH:MM:SS' string
                                $rsFin    = $r->rsva_hora_fin;

                                // Solapamiento: el slot se superpone con la reserva
                                $hayOverlap = ($rsInicio < $horaSiguiente && $rsFin > $horaActual)
                                           || ($rsFin === $horaActual);

                                if ($hayOverlap) {
                                    $ocupado           = true;
                                    $reservaEncontrada = $r;
                                    break;
                                }
                            }
                        }
                    @endphp

                    @php
                        $claseCss = 'disponible';
                        if ($ocupado) {
                            // Normalizamos a minúsculas para comparar
                            $estado = strtolower($reservaEncontrada->rsva_estado);
                            $claseCss = ($estado == 'pendiente') ? 'pendiente' : 'reservado';
                        }
                    @endphp

                    <td class="{{ $claseCss }}"
                        data-espacio="{{ $espacio->espacio_id }}"
                        data-hora="{{ $horaActual }}"
                        @if ($ocupado)
                            data-reserva-id="{{ $reservaEncontrada->reserva_id }}"
                            data-user-name="{{ $reservaEncontrada->usuario->user_nombre ?? 'N/A' }}"
                            data-user-email="{{ $reservaEncontrada->usuario->user_correo ?? 'N/A' }}"
                            data-espacio-nombre="{{ $espacio->esp_nombre }}"
                            data-user-phone="{{ $reservaEncontrada->usuario->user_telefono ?? 'Sin número' }}"
                            data-hora-inicio="{{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_inicio)->format('g:i A') }}"
                            data-hora-fin="{{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_fin)->format('g:i A') }}"
                            data-hora-fin-raw="{{ $reservaEncontrada->rsva_hora_fin }}"
                            data-fecha="{{ $reservaEncontrada->rsva_fecha }}"
                        @endif>

                        @if ($ocupado)
                            @if(strtolower($reservaEncontrada->rsva_estado) == 'pendiente')
                                Pendiente<br>
                            @else
                                Ocupado<br>
                            @endif
                            <small>
                                ({{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_inicio)->format('g:i A') }}
                                 –
                                {{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_fin)->format('g:i A') }})
                            </small>
                            <span class="countdown-badge"
                                  data-fin="{{ $reservaEncontrada->rsva_fecha }} {{ $reservaEncontrada->rsva_hora_fin }}">
                            </span>
                        @else
                            Disponible
                        @endif

                    </td>
                @endforeach
            </tr>

            @php $slotInicio->addHour(); @endphp

        @endwhile
    </tbody>
</table>

{{-- MODAL PARA CAMBIAR HORA DE FIN --}}
<div id="modal-hora" class="modal-overlay" style="display:none;">
    <div class="modal-content animate-pop">
        <h3 style="margin-top:0;">Actualizar Fin de Reserva</h3>
        <p>¿A qué hora deseas que finalice esta reserva?</p>
        <form action="{{ route('admin.reservas.actualizar_hora') }}" method="POST">
            @csrf
            <input type="hidden" name="reserva_id" id="modal-reserva-id">
            <input type="time" name="nueva_hora_fin" id="modal-hora-fin" class="input-hora-grande" min="06:00" max="20:00" required>
            
            <div class="modal-actions" style="margin-top: 20px; display:flex; gap:10px; justify-content:center;">
                <button type="button" class="btn-cancel" onclick="cerrarModalHora()">Cancelar</button>
                <button type="submit" class="btn-save">Guardar Cambio</button>
            </div>
        </form>
    </div>
</div>

</div>

<script src="{{ asset('js_admin/calendario.js') }}"></script>
@endsection
