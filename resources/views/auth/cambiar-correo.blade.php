@extends('layouts.app')

@section('title', 'Cambiar Correo Electrónico')

@push('styles')
    <link rel="stylesheet" href="/css/auth/verificacion.css">
@endpush

@section('content')
    <section class="isolated-verify-container">
        <div class="verify-card-premium">
            <!-- Body -->
            <div class="card-body">
                <h2 class="card-title">Actualizar tu correo electrónico</h2>
                
                <p class="card-text">
                    Ingresa tu nuevo correo electrónico. Después deberás verificarlo para continuar usando tu cuenta.
                </p>

                <!-- Mostrar errores si los hay -->
                @if ($errors->any())
                    <div class="alert-box alert-error">
                        <p class="alert-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon-danger"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> <strong>Error:</strong> {{ $errors->first() }}
                        </p>
                    </div>
                @endif

                <form action="{{ route('verification.cambiar-correo') }}" method="POST" class="form-cambiar-correo">
                    @csrf

                    <!-- Correo actual (solo lectura) -->
                    <div class="form-group">
                        <label class="form-label">Correo Actual</label>
                        <input 
                            type="email" 
                            value="{{ $user->user_correo }}"
                            disabled
                            class="form-input-disabled">
                    </div>

                    <!-- Nuevo Correo -->
                    <div class="form-group">
                        <label for="correo_nuevo" class="form-label">Nuevo Correo</label>
                        <input 
                            type="email" 
                            id="correo_nuevo" 
                            name="correo_nuevo" 
                            placeholder="tu-nuevo-correo@example.com"
                            required
                            autofocus
                            class="form-input">
                    </div>

                    <!-- Confirmar Correo -->
                    <div class="form-group form-group-last">
                        <label for="correo_confirmacion" class="form-label">Confirmar Correo</label>
                        <input 
                            type="email" 
                            id="correo_confirmacion" 
                            name="correo_confirmacion" 
                            placeholder="tu-nuevo-correo@example.com"
                            required
                            class="form-input">
                    </div>

                    <!-- Botones -->
                    <div class="form-buttons">
                        <button type="submit" class="form-button-submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Actualizar Correo
                        </button>
                        <a href="{{ route('verification.notice') }}" class="form-button-back">
                            ← Volver
                        </a>
                    </div>
                </form>

                <p class="form-footer">
                    © {{ date('Y') }} <strong>Co-Labs</strong> · Acceso seguro
                </p>
            </div>
        </div>
    </section>
@endsection
