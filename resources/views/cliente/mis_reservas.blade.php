@extends('layouts.cliente')

@section('title', 'Mis Reservas - COLABS')

@section('content')
<div class="mis-reservas-header animate-fade-up">
    <h2>Mis Reservas</h2>
    <p class="text-muted text-center">Gestiona todas tus reservas, su estado, y su historial.</p>
</div>

@if(session('success'))
<div class="contenedor-alerta">
    <div class="alert alert-success mis-reservas-list">
        {{ session('success') }}
    </div>
</div>
@endif

<div class="mis-reservas-toolbar mis-reservas-list animate-fade-up delay-100">
    <span class="mis-reservas-toolbar-label">Ordenar por</span>
    <div class="mis-reservas-sort"
         data-active="{{ ($orden ?? 'recientes') === 'prioridad' ? 'prioridad' : 'recientes' }}"
         aria-label="Ordenar reservas">
        <a href="{{ route('cliente.mis_reservas', ['orden' => 'recientes']) }}"
           class="mis-reservas-sort-option {{ ($orden ?? 'recientes') === 'recientes' ? 'active' : '' }}">
            Más recientes
        </a>
        <a href="{{ route('cliente.mis_reservas', ['orden' => 'prioridad']) }}"
           class="mis-reservas-sort-option {{ ($orden ?? 'recientes') === 'prioridad' ? 'active' : '' }}">
            Prioridad
        </a>
    </div>
</div>

<div id="reservasListado" class="espacios-listado mis-reservas-list">
    @forelse($reservas as $index => $reserva)
        @php
            // Obtener imagen del espacio
            $imagen = \App\Models\Imagen::where('espacio_id', $reserva->espacio_id)->first();
            $imgSrc = $imagen ? $imagen->foto : null;
            
            // Calcular info dinámica (Horas y Costo)
            $horaInicio = \Carbon\Carbon::parse($reserva->hora_inicio);
            $horaFin = \Carbon\Carbon::parse($reserva->hora_fin);
            $diferencia_horas = max(0, $horaInicio->diffInMinutes($horaFin) / 60);
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
        
        <div class="reserva-card-main {{ 'border-' . strtolower($reserva->estado) }} animate-fade-up" style="animation-delay: {{ 0.15 + ($index * 0.05) }}s;">
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
                    <div class="reserva-meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> <strong>{{ $fecha_formato }}</strong></div>
                    <div class="reserva-meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> <strong>{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('h:i A') }}</strong> ({{ $diferencia_horas }}h)</div>
                </div>

                {{-- Badge de descuento --}}
                @if($tieneDescuento)
                    <div class="mt-10">
                        <span class="desc-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg> {{ $descuentoPorcentaje }}% OFF · Paquete {{ $paqueteHoras }}h
                        </span>
                    </div>
                @endif
            </div>

            <div class="reserva-actions-column">
                <div class="reserva-total-box">
                    <div class="reserva-total-label">
                        @if($tieneDescuento)
                            Total <span class="total-desc-label">(con desc.)</span>:
                        @else
                            Total:
                        @endif
                    </div>
                    <div class="reserva-total-amount {{ $tieneDescuento ? 'total-amount-desc' : '' }}">
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
        <div class="empty-state animate-fade-up delay-200">
            <div class="empty-state-icon">🔍</div>
            <h3>¡Aún no tienes reservas!</h3>
            <p class="text-muted mb-20">Explora nuestros espacios y encuentra el lugar ideal para trabajar.</p>
            <a href="{{ route('cliente.buscar_espacios') }}" class="btn-principal">Buscar Espacios</a>
        </div>
    @endforelse
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sortControl      = document.querySelector('.mis-reservas-sort');
        const reservationsList = document.getElementById('reservasListado');

        if (!sortControl || !reservationsList || !window.fetch) return;

        /* ── URLs generadas por Laravel ── */
        const DETALLES_URL_BASE = '{{ rtrim(route("cliente.detalles_reserva", 0), "0") }}';
        const BUSCAR_URL        = '{{ route("cliente.buscar_espacios") }}';
        const FALLBACK_IMG      = '{{ asset("uploads/OF1 .jpeg") }}';

        /* ── Helpers de formato ── */
        const MESES     = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        const formatFecha = (ymd) => { const [y,m,d] = ymd.split('-').map(Number); return `${d} de ${MESES[m-1]}, ${y}`; };
        const formatHora  = (hhmm) => { const [h,min] = hhmm.split(':').map(Number); return `${(h%12||12).toString().padStart(2,'0')}:${min.toString().padStart(2,'0')} ${h>=12?'PM':'AM'}`; };
        const formatCOP   = (n) => Math.round(n).toLocaleString('es-CO');
        const parseDesc   = (desc) => {
            if (!desc || !desc.includes('[INFO PAQUETE:')) return null;
            const pct = desc.match(/descuento del (\d+)%/);
            const hrs = desc.match(/paquete de (\d+) horas/);
            return pct ? { pct: parseInt(pct[1]), hrs: hrs ? parseInt(hrs[1]) : 0 } : null;
        };

        /* ── Skeleton loader ── */
        const showSkeleton = () => {
            if (!document.getElementById('skeleton-pulse-style')) {
                const s = document.createElement('style');
                s.id    = 'skeleton-pulse-style';
                s.textContent = `@keyframes skeleton-pulse{0%,100%{opacity:1}50%{opacity:.5}}`;
                document.head.appendChild(s);
            }
            const card = `
                <div style="display:flex;gap:16px;background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;border:1px solid #eee;animation:skeleton-pulse 1.2s ease-in-out infinite;">
                    <div style="width:110px;height:90px;border-radius:8px;background:#e5e7eb;flex-shrink:0;"></div>
                    <div style="flex:1;display:flex;flex-direction:column;gap:10px;justify-content:center;">
                        <div style="height:16px;width:55%;background:#e5e7eb;border-radius:6px;"></div>
                        <div style="height:12px;width:80%;background:#f3f4f6;border-radius:6px;"></div>
                        <div style="height:12px;width:40%;background:#f3f4f6;border-radius:6px;"></div>
                    </div>
                </div>`;
            reservationsList.innerHTML = card.repeat(3);
        };

        /* ── Animación escalonada de entrada ── */
        const animateCards = () => {
            reservationsList.querySelectorAll('.reserva-card-main').forEach((card, i) => {
                card.style.cssText = 'opacity:0;transform:translateY(16px);transition:none;';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    card.style.cssText = `opacity:1;transform:translateY(0);transition:opacity .3s ease ${i*50}ms,transform .3s ease ${i*50}ms;`;
                }));
            });
        };

        /* ── Renderizar card desde JSON ── */
        const renderCard = (r) => {
            const inicio     = new Date(`2000-01-01T${r.hora_inicio}`);
            const fin        = new Date(`2000-01-01T${r.hora_fin}`);
            const horas      = Math.max(0, (fin - inicio) / 3600000);
            const descuento  = parseDesc(r.descripcion);
            const totalBase  = horas * r.esp_precio_hora;
            const totalFinal = descuento ? totalBase * (1 - descuento.pct / 100) : totalBase;
            const estadoLow  = r.estado.toLowerCase();
            const idPad      = String(r.reserva_id).padStart(4, '0');
            const descCorta  = r.esp_descripcion
                ? r.esp_descripcion.substring(0, 80) + (r.esp_descripcion.length > 80 ? '...' : '')
                : '';

            const descBadge = descuento ? `
                <div style="margin-top:10px;">
                    <span style="display:inline-flex;align-items:center;gap:.4rem;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#065f46;padding:.3rem .75rem;border-radius:999px;font-size:.78rem;font-weight:700;border:1px solid #6ee7b7;box-shadow:0 1px 4px rgba(16,185,129,.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg> ${descuento.pct}% OFF · Paquete ${descuento.hrs}h
                    </span>
                </div>` : '';

            const totalLabel = descuento
                ? `Total <span style="font-size:.7rem;color:#059669;font-weight:700;">(con desc.)</span>:`
                : 'Total:';

            const calificarBtn = (r.estado === 'Finalizada' && !r.ya_calificado) ? `
                <button type="button" data-espacio-id="${r.espacio_id}" data-reserva-id="${r.reserva_id}"
                        onclick="openReviewModal(this.dataset.espacioId, this.dataset.reservaId)"
                        <span style="display:inline-flex;align-items:center;gap:4px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> Calificar</span></button>` : '';

            return `
                <div class="reserva-card-main border-${estadoLow}">
                    <div class="reserva-id-tag">#${idPad}</div>
                    <img src="${r.img_src}" alt="${r.esp_nombre}"
                         data-fallback="${FALLBACK_IMG}" onerror="this.src=this.getAttribute('data-fallback')">
                    <div class="reserva-info-body">
                        <h3>${r.esp_nombre}</h3>
                        <p>${descCorta}</p>
                        <div class="reserva-meta">
                            <div style="display:inline-flex;align-items:center;gap:4px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> <strong>${formatFecha(r.fecha)}</strong></div>
                            <div style="display:inline-flex;align-items:center;gap:4px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> <strong>${formatHora(r.hora_inicio)} - ${formatHora(r.hora_fin)}</strong> (${horas}h)</div>
                        </div>
                        ${descBadge}
                    </div>
                    <div class="reserva-actions-column">
                        <div class="reserva-total-box">
                            <div class="reserva-total-label">${totalLabel}</div>
                            <div class="reserva-total-amount" style="${descuento ? 'color:#059669;' : ''}">$${formatCOP(totalFinal)}</div>
                        </div>
                        <div class="reserva-badge badge-${estadoLow}">${r.estado.toUpperCase()}</div>
                        ${calificarBtn}
                        <a href="${DETALLES_URL_BASE}${r.reserva_id}" class="btn-reservar mt-5">Ver Detalles →</a>
                    </div>
                </div>`;
        };

        /* ── Listener del slider ── */
        sortControl.addEventListener('click', async (event) => {
            const link = event.target.closest('a');
            if (!link || link.classList.contains('active') || link.origin !== window.location.origin) return;

            event.preventDefault();
            sortControl.classList.add('is-loading');

            // Feedback visual inmediato en el slider
            sortControl.dataset.active = link.href.includes('orden=prioridad') ? 'prioridad' : 'recientes';
            sortControl.querySelectorAll('a').forEach(opt => opt.classList.toggle('active', opt === link));
            showSkeleton();

            try {
                const response = await fetch(link.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });

                if (!response.ok) throw new Error();

                const { reservas } = await response.json();

                if (!reservas || reservas.length === 0) {
                    reservationsList.innerHTML = `
                        <div class="empty-state animate-fade-up" style="animation-delay:.2s;">
                            <div class="empty-state-icon">🔍</div>
                            <h3>¡Aún no tienes reservas!</h3>
                            <p class="text-muted mb-20">Explora nuestros espacios y encuentra el lugar ideal para trabajar.</p>
                            <a href="${BUSCAR_URL}" class="btn-principal">Buscar Espacios</a>
                        </div>`;
                } else {
                    reservationsList.innerHTML = reservas.map(renderCard).join('');
                    animateCards();
                }

                window.history.pushState({}, '', link.href);
            } catch {
                window.location.href = link.href;
            } finally {
                sortControl.classList.remove('is-loading');
            }
        });

        window.addEventListener('popstate', () => window.location.reload());
    });
</script>
@endsection
