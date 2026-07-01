@extends('layouts.app')

@section('title', 'Verifica tu correo')

@push('styles')
    <link rel="stylesheet" href="/css/auth/verificacion.css">
@endpush

@section('content')
    <section class="isolated-verify-container" 
             data-verificacion-config 
             data-last-email-sent="{{ session('last_email_sent_at') ?? 0 }}">
             
        <div class="verify-card-premium">
            <!-- Body -->
            <div class="card-body">
                <div class="icon-wrapper"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><path d="m2 4 10 8 10-8"/></svg></div>
                
                <h2 class="card-title">Verifica tu correo electrónico</h2>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert-box alert-success">
                        <p class="alert-text" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-danger"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg> <strong>¡Enviado!</strong> Se ha enviado un nuevo enlace de un solo uso. Revisa tu bandeja de entrada.
                        </p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-box alert-error">
                        <p class="alert-text" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-warning"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> <strong>Aviso:</strong> {{ session('error') }}
                        </p>
                    </div>
                @endif

                <p class="card-text">
                    La confidencialidad es nuestra prioridad. Por favor confirma la propiedad de tu cuenta haciendo clic en el enlace que enviamos a <strong>{{ auth()->user()->user_correo }}</strong>.
                </p>

                @if (session('status') == 'verification-email-changed')
                    <div class="alert-box alert-success">
                        <p class="alert-text" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-success"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Se ha enviado un nuevo enlace de verificación a tu nuevo correo. Por favor revisa tu bandeja de entrada.
                        </p>
                    </div>
                @endif

                @php
                    $intentosReales = session('intentos') ?? \Illuminate\Support\Facades\Session::get('reenvios_verificacion', 0);
                @endphp

                @if ($intentosReales >= 3)
                    <button type="button" class="btn-gold-pill" disabled>Límite máximo alcanzado <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-inline"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></button>
                    <p class="limit-msg">
                        Por seguridad tu cuenta está en enfriamiento. Recarga más tarde.
                    </p>
                @else
                    <form action="{{ route('verification.send') }}" method="POST" id="form-reenviar">
                        @csrf
                        <button type="submit" id="btn-reenviar" class="btn-gold-pill">
                            Reenviar llave de acceso
                        </button>
                    </form>
                @endif

                <!-- Botón para cambiar correo -->
                <a href="{{ route('verification.form-cambiar-correo') }}" class="btn-change-email">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-inline"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg> Cambiar correo
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-ghost">
                        Cerrar sesión y volver al inicio
                    </button>
                </form>

                <p class="verify-footer">
                    © {{ date('Y') }} <strong>Co-Labs</strong> · Acceso de seguridad verificado
                </p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth/verificacion.js') }}"></script>
@endpush
