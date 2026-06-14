<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') - Co•labs</title>

    <link rel="icon" href="{{ asset('ASSETS/logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    {{-- CSS del panel admin. Mueve super.css a public/css/admin/admin.css --}}
    <link rel="stylesheet" href="/css/admin/admin.css">

    @yield('styles')
</head>

<body data-session-status="{{ session('status') ?? session('success') ?? session('error') ?? '' }}">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('ASSETS/logo.png') }}" alt="Logo Colabs">
            <label>Co•labs</label>
        </div>

        <nav>
            <ul>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="{{ request()->routeIs('admin.espacios*') ? 'active' : '' }}">
                    <a href="{{ route('admin.espacios.index') }}">Espacios</a>
                </li>
                <li class="{{ request()->routeIs('admin.reservas.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.reservas.index') }}">Reservas</a>
                </li>

                {{-- Submenú de solicitudes --}}
                <li>
                    <details {{ request()->routeIs('admin.reservas.pendientes', 'admin.reservas.finalizadas') ? 'open' : '' }}>
                        <summary>Solicitudes de reservas</summary>
                        <ul>
                            <li class="{{ request()->routeIs('admin.reservas.pendientes') ? 'active' : '' }}">
                                <a href="{{ route('admin.reservas.pendientes') }}">Pendientes</a>
                            </li>
                            <li class="{{ request()->routeIs('admin.reservas.finalizadas') ? 'active' : '' }}">
                                <a href="{{ route('admin.reservas.finalizadas') }}">Finalizadas</a>
                            </li>
                        </ul>
                    </details>
                </li>

                {{-- Analítica --}}
                <li class="{{ request()->routeIs('admin.analitica') ? 'active' : '' }}">
                    <a href="{{ route('admin.analitica') }}">Analítica</a>
                </li>

                {{-- Reportes --}}
                <li>
                    <details {{ request()->routeIs('admin.reportes.*') ? 'open' : '' }}>
                        <summary>Reportes</summary>
                        <ul>
                            <li class="{{ request()->routeIs('admin.reportes.espacios') ? 'active' : '' }}">
                                <a href="{{ route('admin.reportes.espacios') }}">Espacios</a>
                            </li>
                            <li class="{{ request()->routeIs('admin.reportes.pagos') ? 'active' : '' }}">
                                <a href="{{ route('admin.reportes.pagos') }}">Ingresos</a>
                            </li>
                        </ul>
                    </details>
                </li>

                {{-- Copias de seguridad --}}
                <li>
                    <details {{ request()->routeIs('admin.copias', 'admin.backup.logs') ? 'open' : '' }}>
                        <summary>Copias de seguridad</summary>
                        <ul>
                            <li class="{{ request()->routeIs('admin.copias') ? 'active' : '' }}">
                                <a href="{{ route('admin.copias') }}">Gestionar</a>
                            </li>
                            <li class="{{ request()->routeIs('admin.backup.logs') ? 'active' : '' }}">
                                <a href="{{ route('admin.backup.logs') }}">Historial</a>
                            </li>
                        </ul>
                    </details>
                </li>

                {{-- Solo visible para SuperAdmin (rol_id = 1) --}}
                @if(auth()->user()->rol_id == 1)
                <li class="{{ request()->routeIs('admin.gestion_admin*') ? 'active' : '' }}">
                    <a href="{{ route('admin.gestion_admin.index') }}">Gestión administradores</a>
                </li>
                @endif
            </ul>
        </nav>

    </aside>

    {{-- ===================== CONTENEDOR PRINCIPAL ===================== --}}
    <div class="main-wrapper">

        {{-- HEADER --}}
        <header class="header">
            <div class="left">@yield('page-title', 'Dashboard')</div>
            <div class="right">
                <button class="new-btn" onclick="document.getElementById('logout-modal').style.display='flex'">
                    Cerrar Sesión
                </button>
            </div>
        </header>

        {{-- CONTENIDO DE CADA PÁGINA --}}
        <main class="content">
            @yield('content')
        </main>

    </div>{{-- /.main-wrapper --}}

    {{-- ===== MODAL CONFIRMAR CIERRE DE SESIÓN ===== --}}
    <div id="logout-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:2rem 2.5rem; max-width:380px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18); text-align:center; animation:fadeInUp .2s ease;">
            <div style="font-size:2.5rem; margin-bottom:.5rem;">🔒</div>
            <h3 style="margin:0 0 .5rem; font-size:1.2rem; color:#1a1a2e;">¿Cerrar sesión?</h3>
            <p style="margin:0 0 1.5rem; color:#6b7280; font-size:.95rem;">¿Estás seguro de que deseas cerrar tu sesión como administrador?</p>
            <div style="display:flex; gap:.75rem; justify-content:center;">
                <button
                    onclick="document.getElementById('logout-modal').style.display='none'"
                    style="padding:.6rem 1.4rem; border-radius:8px; border:none; background:#9ca3af; color:#fff; font-size:.95rem; cursor:pointer; font-weight:600; transition:background .2s;"
                    onmouseover="this.style.background='#6b7280'" onmouseout="this.style.background='#9ca3af'">
                    Cancelar
                </button>
                <button
                    onclick="document.getElementById('logout-form-admin').submit()"
                    style="padding:.6rem 1.4rem; border-radius:8px; border:none; background:#ef4444; color:#fff; font-size:.95rem; cursor:pointer; font-weight:600; transition:background .2s;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#ef4444'">
                    Sí, cerrar sesión
                </button>
            </div>
        </div>
    </div>

    <form id="logout-form-admin" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #logout-modal {
            backdrop-filter: blur(3px);
        }
    </style>

    <div id="snackbar"></div>

    <script>
        function snack(msg) {
            const bar = document.getElementById('snackbar');
            if (!bar || !msg) return;
            bar.innerHTML = msg;
            bar.classList.add('show');
            setTimeout(() => {
                bar.classList.remove('show');
            }, 3500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const statusMsg = document.body.getAttribute('data-session-status');
            if (statusMsg) {
                snack(statusMsg);
            }
        });
    </script>

    @yield('scripts')

    {{-- ===== PROTECCIÓN ANTI-DOBLE CLICK (GLOBAL) ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            /**
             * Deshabilita el botón submit de cualquier formulario al ser enviado,
             * para evitar envíos duplicados por doble clic.
             */
            document.addEventListener('submit', function (e) {
                const form = e.target;
                // Buscar el botón que disparó el submit (o el primero disponible)
                const btn = form.querySelector('[type="submit"]:not([data-no-disable])');
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    btn.dataset.originalText = btn.innerHTML;
                    btn.innerHTML = '<span style="opacity:.7">Procesando...</span>';
                }
            });

            /**
             * Protege el botón de confirmación del modal de logout (que llama 
             * form.submit() directamente en lugar de usar type="submit").
             */
            const btnConfirmarLogout = document.querySelector(
                '#logout-modal button[onclick*="logout-form-admin"]'
            );
            if (btnConfirmarLogout) {
                btnConfirmarLogout.addEventListener('click', function () {
                    this.disabled = true;
                    this.innerHTML = '<span style="opacity:.7">Cerrando...</span>';
                });
            }
        });
    </script>

    {{-- ===== AUTO-SINCRONIZACIÓN DE ESTADOS DE RESERVAS ===== --}}
    {{-- Llama al servidor cada 60 s para marcar como "finalizada" las reservas vencidas --}}
    <script>
        (function() {
            const URL = "{{ route('admin.reservas.sincronizar_estados') }}";
            const TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ??
                "{{ csrf_token() }}";

            function sincronizar() {
                fetch(URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': TOKEN,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.actualizadas > 0) {
                            console.info(`[Colabs] ${data.actualizadas} reserva(s) finalizadas a las ${data.hora}`);
                        }
                    })
                    .catch(() => {}); // silenciar errores de red
            }

            // Ejecutar de inmediato al cargar la página y luego cada 60 segundos
            sincronizar();
            setInterval(sincronizar, 60_000);
        })();
    </script>
</body>

</html>

