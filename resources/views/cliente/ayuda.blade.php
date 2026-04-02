@extends('layouts.cliente')

@section('title', 'Ayuda y Soporte - COLABS')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/ayuda.css') }}">
@endsection

@section('content')
    <div class="ayuda-container">
        <div class="help-header">
            <h1>¿Cómo usar <span>COLABS</span>?</h1>
            <p>Guía rápida para sacar el máximo provecho a tus espacios de trabajo.</p>
        </div>

        <div class="guia-pasos">
            <!-- Paso 1 -->
            <div class="paso-item">
                <div class="paso-numero">1</div>
                <div class="paso-contenido">
                    <h3>Busca tu espacio ideal</h3>
                    <p>En el panel principal, utiliza los filtros para encontrar el espacio que mejor se adapte a tus necesidades. Puedes filtrar por <strong>tipo de oficina, capacidad de personas y precio máximo</strong>.</p>
                </div>
            </div>

            <!-- Paso 2 -->
            <div class="paso-item">
                <div class="paso-numero">2</div>
                <div class="paso-contenido">
                    <h3>Verifica la disponibilidad</h3>
                    <p>Al seleccionar un espacio, elige la fecha y el rango de horas. Nuestro sistema verificará en tiempo real si el bloque está libre. Si está ocupado, ¡no te preocupes! Te sugeriremos espacios similares disponibles en ese mismo horario.</p>
                </div>
            </div>
            
            <!-- Paso 3 -->
            <div class="paso-item">
                <div class="paso-numero">3</div>
                <div class="paso-contenido">
                    <h3>Confirma tu Reserva</h3>
                    <p>Una vez verificado el horario, indica el número de invitados y una breve descripción del uso que le darás al espacio. Al confirmar, tu solicitud será enviada al administrador para su aprobación.</p>
                </div>
            </div>

            <!-- Paso 4 -->
            <div class="paso-item">
                <div class="paso-numero">4</div>
                <div class="paso-contenido">
                    <h3>Gestiona tus Solicitudes</h3>
                    <p>En la sección <strong>"Mis Reservas"</strong> puedes ver el estado de tus solicitudes (Pendiente, Aceptada o Cancelada). También podrás ver los detalles completos y la ubicación exacta del espacio.</p>
                </div>
            </div>

            <!-- Paso 5 -->
            <div class="paso-item">
                <div class="paso-numero">5</div>
                <div class="paso-contenido">
                    <h3>Califica tu experiencia</h3>
                    <p>Después de disfrutar de tu reserva, puedes dejar una calificación y un comentario. Tu opinión ayuda a otros miembros de la comunidad y a nosotros a mejorar el servicio.</p>
                </div>
            </div>
        </div>

        <div class="soporte-footer">
            <h3>¿Aún tienes dudas?</h3>
            <p>Si presentas problemas con el acceso al local o necesitas soporte técnico inmediato, contáctanos.</p>
            <a href="mailto:colabsbq@gmail.com" class="btn-contacto">Contactar a Soporte</a>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('cliente.index') }}" style="color: #666; font-size: 0.9rem;">← Volver al inicio</a>
        </div>
    </div>
@endsection