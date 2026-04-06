{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Colabs')</title>

    <link rel="icon" href="{{ asset('ASSETS/logo.png') }}" type="image/png">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    {{-- CSS principal --}}
    <link rel="stylesheet" href="/css/index.css">

    @stack('styles')
</head>
<body data-session-status="{{ session('status') ?? session('success') ?? session('error') ?? '' }}">

    {{-- ===== HEADER ===== --}}
    <header>
        <nav>
            {{-- Izquierda: logo + menu --}}
            <div class="nav-left">
                <a href="{{ route('inicio') }}" class="logo">
                    <img src="{{ asset('ASSETS/logo.png') }}" alt="Colabs">
                </a>
            </div>

            {{-- Menu centrado --}}
            <ul class="menu">
                <li><a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('nosotros') }}" class="{{ request()->routeIs('nosotros') ? 'active' : '' }}">Nosotros</a></li>
                <li><a href="{{ route('ubicacion') }}" class="{{ request()->routeIs('ubicacion') ? 'active' : '' }}">Encuentranos</a></li>
                <li><a href="{{ route('servicios') }}" class="{{ request()->routeIs('servicios') ? 'active' : '' }}">Servicios</a></li>
            </ul>
 
            {{-- Derecha: botones de sesion --}}
            <div class="nav-right">
                <div class="botones-sesion">
                    @guest
                        <a href="{{ route('registrarse.mostrar') }}" class="btn-sesion registrarse">Registrarse</a>
                        <a href="{{ route('login') }}" class="btn-sesion iniciar">Iniciar sesion</a>
                    @else
                        @php
                            $usuarioAutenticado = auth()->user();
                            $cuentaNoVerificada = !$usuarioAutenticado->hasVerifiedEmail();
                            $estaEnPantallaVerificacion = request()->routeIs('verification.notice');
                            $debeCompletarPerfilGoogle = !empty($usuarioAutenticado->google_id)
                                && ((int) ($usuarioAutenticado->user_telefono ?? 0) <= 0 || empty($usuarioAutenticado->numero_documento));
                            $estaEnPantallaCompletarPerfil = request()->routeIs('google.perfil.completar');

                            $rutaPanel = in_array($usuarioAutenticado->rol_id, [1, 2])
                                ? route('admin.dashboard')
                                : route('cliente.index');

                            $rutaBotonPanel = $rutaPanel;
                            $textoBotonPanel = 'Ir a tu panel';
                            $mensajeBotonPanel = null;

                            if ($cuentaNoVerificada) {
                                $rutaBotonPanel = $estaEnPantallaVerificacion ? 'javascript:void(0)' : route('verification.notice');
                                $textoBotonPanel = 'Verificar cuenta';
                                if ($estaEnPantallaVerificacion) {
                                    $mensajeBotonPanel = 'Ya estas en la pantalla para reenviar el correo.';
                                }
                            } elseif ($debeCompletarPerfilGoogle) {
                                $rutaBotonPanel = $estaEnPantallaCompletarPerfil ? 'javascript:void(0)' : route('google.perfil.completar');
                                $textoBotonPanel = 'Completar perfil';
                                if ($estaEnPantallaCompletarPerfil) {
                                    $mensajeBotonPanel = 'Ya estas completando tu perfil.';
                                }
                            }
                        @endphp

                        <a href="{{ $rutaBotonPanel }}"
                           class="btn-sesion iniciar"
                           @if($mensajeBotonPanel) onclick="snack('{{ $mensajeBotonPanel }}')" @endif>
                            {{ $textoBotonPanel }}
                        </a>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    {{-- ===== CONTENIDO ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer>
        <p>&copy; {{ date('Y') }} Colabs. Todos los derechos reservados.</p>
    </footer>

    {{-- Snackbar Global --}}
    <div id="snackbar"></div>

    <script src="{{ asset('js/global.js') }}"></script>

    @stack('scripts')
</body>
</html>
