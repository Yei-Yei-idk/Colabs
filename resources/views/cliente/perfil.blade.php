@extends('layouts.cliente')

@section('title', 'Dashboard de Perfil - Co-Labs')

@section('content')
@php
    $esCuentaGoogle = !empty($usuario->google_id);
@endphp

<section id="perfil-dashboard" class="zero-scroll-container animate-fade-in">
    <div class="dash-perfil-wrapper">
        <div class="dash-sidebar">
            <div class="user-id-card">
                <div
                    class="avatar-dash {{ $esCuentaGoogle ? '' : ($usuario->avatar_color ?? 'blue') }}"
                    @if($esCuentaGoogle && !empty($usuario->avatar))
                        style="background-image: url('{{ $usuario->avatar }}'); background-size: cover; background-position: center; color: transparent;"
                    @endif
                >
                    @if(!$esCuentaGoogle || empty($usuario->avatar))
                        {{ $usuario->avatar_initial ?? 'U' }}
                    @endif
                    <span class="badge-status-dot {{ $usuario->email_verified_at ? 'verified' : 'pending' }}"></span>
                </div>
                <h2>{{ $usuario->user_nombre }}</h2>
                <div class="id-tag">Miembro de Co-Labs</div>

                @if($esCuentaGoogle)
                    <div class="security-note" style="margin-top: 16px; border-left-color: #4285F4;">
                        Cuenta vinculada con Google.
                    </div>
                @endif

                @if(!$usuario->email_verified_at)
                    <div class="alert-verification-warning">
                        <span>Cuenta no verificada</span>
                        <p>Por favor confirma tu correo o corrige tu direccion en el formulario.</p>
                    </div>
                @endif
            </div>

            <div class="dash-stats">
                <div class="stat-bubble">
                    <span class="count">{{ $totalReservas }}</span>
                    <span class="label">Reservas</span>
                </div>
                <div class="stat-bubble">
                    <span class="count">{{ $usuario->email_verified_at ? 'Si' : 'No' }}</span>
                    <span class="label">Verificada</span>
                </div>
            </div>
        </div>

        <div class="dash-main-content">
            @if(session('success'))
                <div class="dash-alert success animate-slide-in">
                    <span class="icon">Listo:</span> {{ session('success') }}
                </div>
            @endif

            <form id="formPerfil" method="POST" action="{{ route('cliente.perfil.actualizar') }}" class="dash-form">
                @csrf

                <div class="dash-sections-grid">
                    <div class="dash-card-section">
                        <div class="section-title">
                            <span class="icon">Perfil</span>
                            <h3>Datos de Contacto</h3>
                        </div>

                        <div class="form-row-dash">
                            <div class="field-dash">
                                <label for="nombre">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" value="{{ $usuario->user_nombre }}" required>
                            </div>
                        </div>

                        <div class="form-row-dash multi-col">
                            <div class="field-dash">
                                <label for="numero_documento">Numero de documento</label>
                                <input type="text" id="numero_documento" name="numero_documento" value="{{ $usuario->numero_documento }}" inputmode="numeric" disabled>
                            </div>
                            <div class="field-dash">
                                <label for="email">Correo electronico</label>
                                <input type="email" id="email" name="email" value="{{ $usuario->user_correo }}" @if($esCuentaGoogle) disabled @endif required>
                            </div>
                            <div class="field-dash">
                                <label for="telefono">Telefono</label>
                                <input type="tel" id="telefono" name="telefono" value="{{ $usuario->user_telefono }}" required>
                            </div>
                        </div>
                    </div>

                    @if($esCuentaGoogle)
                        <div class="dash-card-section security">
                            <div class="section-title">
                                <span class="icon">Seguridad</span>
                                <h3>Cuenta de Google</h3>
                            </div>
                            <div class="security-note" style="border-left-color: #4285F4;">
                                Esta cuenta se autentica con Google. El cambio de contraseña se gestiona desde tu cuenta de Google.
                            </div>
                        </div>
                    @else
                        <div class="dash-card-section security">
                            <div class="section-title">
                                <span class="icon">Seguridad</span>
                                <h3>Contraseña</h3>
                            </div>

                            <div class="form-row-dash multi-col">
                                <div class="field-dash">
                                    <label for="password">Contraseña actual</label>
                                    <input type="password" id="password" name="password" placeholder="********">
                                </div>
                                <div class="field-dash">
                                    <label for="newpassword">Nueva contraseña</label>
                                    <input type="password" id="newpassword" name="newpassword" placeholder="********">
                                </div>
                            </div>
                            <div class="security-note">
                                Cambia tu contraseña periodicamente para mantener tu cuenta segura.
                            </div>
                        </div>
                    @endif
                </div>

                <div class="dash-form-actions">
                    <button type="submit" class="btn-save-dash btn-guardar">
                        Guardar cambios del perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/cliente/perfil.js') }}"></script>
@endsection
