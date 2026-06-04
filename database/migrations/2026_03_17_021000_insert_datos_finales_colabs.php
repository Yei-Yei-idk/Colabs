<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('rol')->insertOrIgnore([
            ['rol_id' => 1, 'rol_nombre' => 'Super_admin'],
            ['rol_id' => 2, 'rol_nombre' => 'Admin'],
            ['rol_id' => 3, 'rol_nombre' => 'Usuario'],
        ]);

        DB::table('usuarios')->insertOrIgnore($this->usuarios());
        DB::table('espacios')->insertOrIgnore($this->espacios());
        DB::table('imagenes')->insertOrIgnore($this->imagenes());
        DB::table('reserva')->insertOrIgnore($this->reservas());
        DB::table('calificaciones')->insertOrIgnore($this->calificaciones());
    }

    public function down(): void
    {
        DB::table('calificaciones')->whereBetween('calif_id', [1, 20])->delete();
        DB::table('reserva')->whereBetween('reserva_id', [1, 51])->delete();
        DB::table('imagenes')->whereBetween('img_id', [1, 10])->delete();
        DB::table('espacios')->whereIn('espacio_id', range(101, 110))->delete();
        DB::table('usuarios')->whereIn('id', [1, 3, 4, 5, 6, 7])->delete();
        DB::table('rol')->whereIn('rol_id', [1, 2, 3])->delete();
    }

    private function usuarios(): array
    {
        $fechaVerificacion = now();

        return [
            [
                'id' => 1,
                'numero_documento' => '1',
                'user_nombre' => 'Super_admin',
                'user_correo' => 'admin@colabs.com',
                'google_id' => null,
                'avatar' => null,
                'user_telefono' => 3000000000,
                'email_verified_at' => $fechaVerificacion,
                'verification_token' => null,
                'verification_token_expires_at' => null,
                'user_contrasena' => bcrypt('admin123'),
                'rol_id' => 1,
                'remember_token' => null,
            ],
            [
                'id' => 3,
                'numero_documento' => '1041694277',
                'user_nombre' => 'Hammer Pedroza',
                'user_correo' => 'hammerjr2609@gmail.com',
                'google_id' => null,
                'avatar' => null,
                'user_telefono' => 3244052164,
                'email_verified_at' => $fechaVerificacion,
                'verification_token' => null,
                'verification_token_expires_at' => null,
                'user_contrasena' => bcrypt('cliente123'),
                'rol_id' => 3,
                'remember_token' => null,
            ],
            [
                'id' => 4,
                'numero_documento' => '1042254693',
                'user_nombre' => 'Matthew Caratt',
                'user_correo' => 'carattmatt@gmail.com',
                'google_id' => null,
                'avatar' => null,
                'user_telefono' => 3008163891,
                'email_verified_at' => $fechaVerificacion,
                'verification_token' => null,
                'verification_token_expires_at' => null,
                'user_contrasena' => bcrypt('cliente123'),
                'rol_id' => 3,
                'remember_token' => null,
            ],
            [
                'id' => 5,
                'numero_documento' => '1042254700',
                'user_nombre' => 'Cristhian Guevara',
                'user_correo' => 'cristhiangvara55@gmail.com',
                'google_id' => null,
                'avatar' => null,
                'user_telefono' => 3012126207,
                'email_verified_at' => $fechaVerificacion,
                'verification_token' => null,
                'verification_token_expires_at' => null,
                'user_contrasena' => bcrypt('cliente123'),
                'rol_id' => 3,
                'remember_token' => null,
            ],
            [
                'id' => 6,
                'numero_documento' => '1044217035',
                'user_nombre' => 'Yeiner Jimenez',
                'user_correo' => 'yeinerjimenez2912@gmail.com',
                'google_id' => null,
                'avatar' => null,
                'user_telefono' => 3194919848,
                'email_verified_at' => $fechaVerificacion,
                'verification_token' => null,
                'verification_token_expires_at' => null,
                'user_contrasena' => bcrypt('cliente123'),
                'rol_id' => 3,
                'remember_token' => null,
            ],
            [
                'id' => 7,
                'numero_documento' => '1044215165',
                'user_nombre' => 'Julian Escorcia',
                'user_correo' => 'julian.escorcia29@gmail.com',
                'google_id' => null,
                'avatar' => null,
                'user_telefono' => 3002472365,
                'email_verified_at' => $fechaVerificacion,
                'verification_token' => null,
                'verification_token_expires_at' => null,
                'user_contrasena' => bcrypt('cliente123'),
                'rol_id' => 3,
                'remember_token' => null,
            ],
        ];
    }

    private function espacios(): array
    {
        return [
            [
                'espacio_id' => 101,
                'esp_nombre' => 'Sala Innovación Alpha',
                'esp_descripcion' => 'Un espacio moderno diseñado para fomentar la creatividad y el trabajo colaborativo de alto impacto.',
                'esp_capacidad' => 8,
                'esp_tipo' => 'Sala de reuniones',
                'esp_precio_hora' => 45000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 102,
                'esp_nombre' => 'Oficina Ejecutiva Zen',
                'esp_descripcion' => 'Privacidad y confort en un ambiente minimalista ideal para concentrarse en tareas individuales.',
                'esp_capacidad' => 2,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 30000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 103,
                'esp_nombre' => 'Aula Magna Digital',
                'esp_descripcion' => 'Espacio amplio equipado con tecnología para talleres, capacitaciones y presentaciones.',
                'esp_capacidad' => 25,
                'esp_tipo' => 'Aula',
                'esp_precio_hora' => 120000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 104,
                'esp_nombre' => 'Rincón del Inventor',
                'esp_descripcion' => 'Pequeño pero vibrante, perfecto para sesiones de lluvia de ideas y prototipado rápido.',
                'esp_capacidad' => 4,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 25000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 105,
                'esp_nombre' => 'Sala Eventos Panorama',
                'esp_descripcion' => 'Vista amplia y gran versatilidad para lanzamientos de productos o eventos de networking.',
                'esp_capacidad' => 50,
                'esp_tipo' => 'Sala de eventos',
                'esp_precio_hora' => 250000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 106,
                'esp_nombre' => 'Coworking Global',
                'esp_descripcion' => 'Mesas compartidas con internet de alta velocidad y café para equipos remotos.',
                'esp_capacidad' => 14,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 15000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 107,
                'esp_nombre' => 'Estudio Creativo Prisma',
                'esp_descripcion' => 'Iluminación natural y mobiliario ergonómico para sesiones de diseño, edición y planeación.',
                'esp_capacidad' => 5,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 35000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 108,
                'esp_nombre' => 'Sala Juntas Elite',
                'esp_descripcion' => 'Elegancia y tecnología para reuniones directivas y presentaciones con clientes.',
                'esp_capacidad' => 12,
                'esp_tipo' => 'Sala de reuniones',
                'esp_precio_hora' => 60000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 109,
                'esp_nombre' => 'Foco de Concentración',
                'esp_descripcion' => 'Aislamiento acústico para llamadas importantes o trabajo profundo sin distracciones.',
                'esp_capacidad' => 1,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 20000,
                'esp_estado' => 'Activo',
            ],
            [
                'espacio_id' => 110,
                'esp_nombre' => 'Sala Versátil Beta',
                'esp_descripcion' => 'Configuración flexible que se adapta a reuniones, mentorías y talleres cortos.',
                'esp_capacidad' => 10,
                'esp_tipo' => 'Sala de reuniones',
                'esp_precio_hora' => 40000,
                'esp_estado' => 'Activo',
            ],
        ];
    }

    private function imagenes(): array
    {
        return [
            ['img_id' => 1, 'espacio_id' => 101, 'foto' => 'OF 13.jpg'],
            ['img_id' => 2, 'espacio_id' => 102, 'foto' => 'OF 3.jpeg'],
            ['img_id' => 3, 'espacio_id' => 103, 'foto' => 'OF 9.jpeg'],
            ['img_id' => 4, 'espacio_id' => 104, 'foto' => 'OF1 .jpeg'],
            ['img_id' => 5, 'espacio_id' => 105, 'foto' => 'OF12.jpeg'],
            ['img_id' => 6, 'espacio_id' => 106, 'foto' => 'Of 14 puestos de trabajo .jpeg'],
            ['img_id' => 7, 'espacio_id' => 107, 'foto' => 'Ofic 5 -3.jpeg'],
            ['img_id' => 8, 'espacio_id' => 108, 'foto' => 'Ofic 8.jpeg'],
            ['img_id' => 9, 'espacio_id' => 109, 'foto' => 'ofic 11.jpeg'],
            ['img_id' => 10, 'espacio_id' => 110, 'foto' => 'ofic 5 -2.jpeg'],
        ];
    }

    private function reservas(): array
    {
        $reservas = [];
        $reservaId = 1;
        $clientes = [3, 4, 5, 6, 7];
        $planesDemo = [
            ['Finalizada', [-8, -7, -6, -5, -4], [8, 9, 10, 11, 12], [101, 102, 103, 104, 105]],
            ['Pendiente', [1, 2, 3, 4, 5], [8, 9, 10, 11, 12], [106, 107, 108, 109, 110]],
            ['Aceptada', [2, 3, 4, 5, 1], [14, 15, 16, 17, 18], [101, 102, 103, 104, 105]],
            ['Rechazada', [-7, -6, -5, -4, -3], [13, 14, 15, 16, 17], [106, 107, 108, 109, 110]],
            ['Cancelada', [-6, -5, -4, -3, -2], [15, 16, 17, 18, 19], [101, 102, 103, 104, 105]],
        ];

        $descripcionesDemo = [
            'Finalizada' => [
                'Uso la sala para cerrar una propuesta comercial con dos socios externos y revisar el presupuesto final.',
                'Reserve el espacio para una sesion de trabajo enfocada en preparar entregables de diseno para un cliente.',
                'Necesite el aula para dictar una capacitacion corta sobre herramientas internas del equipo.',
                'Use la oficina para entrevistas presenciales y revision de portafolios de candidatos.',
                'Reserve el espacio para una reunion de producto donde validamos tareas del sprint y proximos responsables.',
            ],
            'Pendiente' => [
                'Solicito el espacio para presentar una propuesta de servicios a un cliente nuevo con apoyo audiovisual.',
                'Necesito una oficina privada para una sesion de planeacion financiera con mi equipo de trabajo.',
                'Quiero usar el aula para un taller practico de onboarding con colaboradores recien vinculados.',
                'Solicito la sala para una reunion de arquitectura y definicion tecnica del proyecto.',
                'Necesito el espacio para una jornada de ideacion con mi equipo antes de entregar el prototipo.',
            ],
            'Aceptada' => 'Reserva aceptada programada para la demostracion del calendario.',
            'Rechazada' => 'Solicitud rechazada disponible para mostrar el estado al cliente.',
            'Cancelada' => 'Reserva cancelada disponible para mostrar el historial del cliente.',
        ];

        foreach ($planesDemo as [$estado, $diasPorCliente, $horasInicio, $espaciosDemo]) {
            foreach ($clientes as $indice => $clienteId) {
                $reservas[] = $this->reserva(
                    $reservaId++,
                    $clienteId,
                    $espaciosDemo[$indice],
                    now()->copy()->addDays($diasPorCliente[$indice])->toDateString(),
                    $horasInicio[$indice],
                    $estado,
                    is_array($descripcionesDemo[$estado])
                        ? $descripcionesDemo[$estado][$indice]
                        : $descripcionesDemo[$estado],
                    2 + $indice
                );
            }
        }

        $clientesResenas = [3, 4, 5, 7];
        $espaciosResenas = array_keys($this->capacidades());
        $diasResenasPorCliente = [
            3 => [5, 4, 3, 2, 0],
            4 => [8, 4, 3, 2, 0],
            5 => [8, 7, 3, 2, 0],
            7 => [8, 7, 6, 5, 1],
        ];
        $contadorResenasPorCliente = array_fill_keys($clientesResenas, 0);
        $descripcionesFinalizadasResenas = [
            'Use este espacio para una revision de contrato con proveedores y cierre de acuerdos pendientes.',
            'Reserve la oficina para grabar una presentacion comercial y coordinar los ultimos ajustes con el equipo.',
            'Utilice el aula para practicar una charla tecnica y revisar las diapositivas con mis companeros.',
            'Trabajamos en una sesion de analisis de datos para preparar el informe mensual del area.',
            'Reserve la sala para una reunion de seguimiento con clientes y definicion de compromisos.',
            'Use el espacio para una jornada de concentracion en la documentacion de procesos internos.',
            'Realizamos una mesa de trabajo para organizar el cronograma de lanzamiento de una campana.',
            'Aprovechamos la sala para resolver pendientes de soporte y coordinar respuestas con el equipo.',
            'Reserve el espacio para revisar indicadores de ventas y preparar acciones para la siguiente semana.',
            'Use la oficina para llamadas con aliados externos y revision privada de documentos sensibles.',
            'Realizamos una sesion de pruebas de una demo antes de presentarla a los interesados.',
            'Reserve el aula para capacitar a dos personas nuevas en el flujo operativo del negocio.',
            'Use este espacio para una reunion creativa de contenido y planificacion de publicaciones.',
            'Trabajamos en la preparacion de una propuesta tecnica para una licitacion cercana.',
            'Reserve la sala para alinear prioridades del proyecto y distribuir tareas del equipo.',
            'Utilice la oficina para preparar entrevistas, revisar hojas de vida y tomar notas de seleccion.',
            'Hicimos una revision presencial de prototipos y ajustes de experiencia de usuario.',
            'Reserve el espacio para una sesion de seguimiento financiero y organizacion de soportes.',
            'Use la sala para validar avances con el cliente y acordar cambios menores de alcance.',
            'Realizamos una jornada corta de planeacion estrategica para definir metas de la proxima entrega.',
        ];

        foreach ($espaciosResenas as $indiceEspacio => $espacioId) {
            for ($resena = 0; $resena < 2; $resena++) {
                $indiceCliente = (($indiceEspacio * 2) + $resena) % count($clientesResenas);
                $clienteId = $clientesResenas[$indiceCliente];
                $indiceResenaCliente = $contadorResenasPorCliente[$clienteId]++;
                $diasAtras = $diasResenasPorCliente[$clienteId][$indiceResenaCliente];

                $reservas[] = $this->reserva(
                    $reservaId++,
                    $clienteId,
                    $espacioId,
                    now()->copy()->subDays($diasAtras)->toDateString(),
                    8 + $resena + ($indiceEspacio % 4),
                    'Finalizada',
                    $descripcionesFinalizadasResenas[($indiceEspacio * 2) + $resena],
                    2 + (($indiceEspacio + $resena) % 4)
                );
            }
        }

        $reservasCalendario = [
            ['Aceptada', 1, 107, 16, 5],
            ['Aceptada', 2, 109, 12, 5],
            ['Aceptada', 3, 110, 13, 7],
            ['Aceptada', 4, 103, 15, 4],
            ['Aceptada', 5, 105, 10, 3],
            ['Aceptada', 5, 107, 14, 5],
        ];

        foreach ($reservasCalendario as [$estado, $diasDesdeHoy, $espacioId, $horaInicio, $clienteId]) {
            $reservas[] = $this->reserva(
                $reservaId++,
                $clienteId,
                $espacioId,
                now()->copy()->addDays($diasDesdeHoy)->toDateString(),
                $horaInicio,
                $estado,
                'Reserva de calendario para mostrar ocupacion realista en la agenda administrativa.',
                3 + ($reservaId % 4)
            );
        }

        return $reservas;

        $espacios = array_keys($this->capacidades());
        $descripcionesFinalizadas = [
            'Jornada de trabajo con cierre de pendientes del equipo comercial.',
            'Reunión de planeación para revisar avances y próximos entregables.',
            'Sesión de capacitación interna con apoyo audiovisual.',
            'Espacio usado para entrevistas y revisión de perfiles.',
            'Taller corto de ideación con equipo de producto.',
            'Trabajo individual concentrado para preparar una presentación.',
        ];

        // 1. Crear 10 reservas finalizadas (1 por espacio)
        foreach (array_slice($espacios, 0, 5) as $indice => $espacioId) {
            $reservas[] = $this->reserva(
                $reservaId++,
                $clientes[$indice % count($clientes)],
                $espacioId,
                now()->copy()->subDays(14 - $indice)->toDateString(),
                9, // 9:00 AM
                'Finalizada',
                $descripcionesFinalizadas[$indice % count($descripcionesFinalizadas)],
                2 + ($indice % 3)
            );
        }

        // 2. Crear 12 reservas de prueba con estados válidos (sin 'Activa')
        $planes = [
            ['Pendiente', false, [7, 8, 9, 10, 11]], // 5 pendientes (una para cada cliente la próxima semana)
            ['Aceptada', false, [3, 6, 9]],          // 3 aceptadas (en el futuro)
            ['Cancelada', true, [4, 12]],            // 2 canceladas (en el pasado)
            ['Rechazada', true, [7, 15]],            // 2 rechazadas (en el pasado)
        ];

        $descripcionesClientes = [
            'Reunión de planeación trimestral para definir objetivos de venta.',
            'Sesión de co-working colaborativo con socios estratégicos.',
            'Taller práctico de diseño de experiencia de usuario.',
            'Entrevistas presenciales para selección de nuevos desarrolladores.',
            'Presentación de propuesta comercial a cliente potencial.',
            'Jornada de ideación y lluvia de ideas para el nuevo producto.',
            'Reunión de revisión de arquitectura de software.',
            'Trabajo individual enfocado en el desarrollo de la API.',
            'Capacitación presencial en herramientas de análisis de datos.',
            'Prueba técnica y mentoría con equipo junior de ingeniería.',
            'Sesión de fotos y grabación de video corporativo.',
            'Reunión de kickoff para el proyecto de migración en la nube.',
        ];

        $contadorDesc = 0;
        foreach ($planes as $indicePlan => [$estado, $pasada, $diasReservas]) {
            foreach ($diasReservas as $indice => $dias) {
                $espacioId = $espacios[($indicePlan * 3 + $indice) % count($espacios)];
                $fecha = $pasada
                    ? now()->copy()->subDays($dias)->toDateString()
                    : now()->copy()->addDays($dias)->toDateString();

                $descripcionCliente = $descripcionesClientes[$contadorDesc % count($descripcionesClientes)];
                $contadorDesc++;

                $reservas[] = $this->reserva(
                    $reservaId++,
                    $clientes[($indicePlan + $indice) % count($clientes)],
                    $espacioId,
                    $fecha,
                    [9, 11, 15][$indice % 3],
                    $estado,
                    $descripcionCliente,
                    2 + $indicePlan + $indice
                );
            }
        }

        return $reservas;
    }

    private function calificaciones(): array
    {
        $comentarios = [
            'El espacio estaba limpio, bien iluminado y el internet funcionó sin interrupciones.',
            'La reserva fue fluida y el lugar resultó cómodo para una reunión corta.',
            'Muy buena ubicación y mobiliario cómodo; volvería a usarlo para sesiones de equipo.',
            'El ambiente ayudó bastante a mantener la concentración durante toda la jornada.',
            'La sala tenía lo necesario para presentar y conversar sin ruido externo.',
            'Buena relación entre precio, comodidad y disponibilidad del espacio.',
            'El acceso fue sencillo y el espacio estaba listo a la hora acordada.',
            'Ideal para trabajar con clientes; la presentación se pudo hacer sin inconvenientes.',
            'El lugar es pequeño, pero muy funcional para llamadas y trabajo profundo.',
            'La experiencia fue positiva, especialmente por la tranquilidad del espacio.',
        ];
        $puntuaciones = [5, 4, 5, 4, 5, 3, 4, 5, 4, 5];
        $clientes = [3, 4, 5, 7];
        $espacios = array_keys($this->capacidades());
        $calificaciones = [];
        $calificacionId = 1;
        $reservaId = 26;

        // 2 calificaciones por espacio, ligadas a reservas finalizadas sin usar a Yeiner.
        foreach ($espacios as $indiceEspacio => $espacioId) {
            for ($resena = 0; $resena < 2; $resena++) {
                $indice = ($indiceEspacio * 2) + $resena;

                $calificaciones[] = [
                    'calif_id' => $calificacionId++,
                    'calif_txt' => $comentarios[$indice % count($comentarios)],
                    'calif_puntuacion' => $puntuaciones[$indice % count($puntuaciones)],
                    'user_id' => $clientes[$indice % count($clientes)],
                    'espacio_id' => $espacioId,
                    'reserva_id' => $reservaId++,
                ];
            }
        }

        return $calificaciones;
    }

    private function reserva(
        int $id,
        int $usuarioId,
        int $espacioId,
        string $fecha,
        int $horaInicio,
        string $estado,
        string $descripcion,
        int $invitados
    ): array {
        return [
            'reserva_id' => $id,
            'rsva_hora_inicio' => sprintf('%02d:00:00', $horaInicio),
            'rsva_hora_fin' => sprintf('%02d:00:00', $horaInicio + 2),
            'rsva_fecha' => $fecha,
            'rsva_estado' => $estado,
            'rsva_descripcion' => $descripcion,
            'rsva_num_invitados' => max(1, min($this->capacidades()[$espacioId], $invitados)),
            'user_id' => $usuarioId,
            'espacio_id' => $espacioId,
        ];
    }

    private function capacidades(): array
    {
        return [
            101 => 8,
            102 => 2,
            103 => 25,
            104 => 4,
            105 => 50,
            106 => 14,
            107 => 5,
            108 => 12,
            109 => 1,
            110 => 10,
        ];
    }
};
