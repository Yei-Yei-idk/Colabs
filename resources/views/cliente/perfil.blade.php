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
                                <input type="text" id="numero_documento" name="numero_documento" value="{{ $usuario->numero_documento }}" inputmode="numeric" readonly>
                            </div>
                            <div class="field-dash">
                                <label for="email">Correo electronico</label>
                                <input type="email" id="email" name="email" value="{{ $usuario->user_correo }}" @if($esCuentaGoogle) readonly @endif required>
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

                            <!-- Requisitos de contraseña -->
                            <div id="password-requirements-profile" style="display: none; margin-top: 10px; margin-bottom: 20px;">
                                <div class="password-rules" style="background: rgba(243, 244, 246, 0.5); padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                    <p style="margin: 0 0 12px 0; font-size: 0.85rem; font-weight: 700; color: #374151;">La nueva contraseña debe incluir:</p>
                                    <ul id="requirement-list" style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                        <li id="req-length" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                            <span class="icon">○</span> Mín. 8 caracteres
                                        </li>
                                        <li id="req-mixed" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                            <span class="icon">○</span> Mayús. y minúsc.
                                        </li>
                                        <li id="req-numbers" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                            <span class="icon">○</span> Al menos 1 número
                                        </li>
                                        <li id="req-symbols" style="font-size: 0.8rem; color: #9ca3af; display: flex; align-items: center; gap: 6px;">
                                            <span class="icon">○</span> Carácter especial
                                        </li>
                                    </ul>
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
