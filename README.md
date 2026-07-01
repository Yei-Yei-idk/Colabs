<div align="center">

# Co-Labs — GRAE
### Sistema de Gestión de Espacios para Coworking

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Google OAuth](https://img.shields.io/badge/Google_OAuth-2.0-4285F4?style=flat-square&logo=google&logoColor=white)](https://console.cloud.google.com)

**GRAE (Gestión y Reservas de Espacios Empresariales)** es una solución tecnológica de nivel corporativo diseñada a medida para **Co-Labs**, orientada a digitalizar, automatizar y optimizar por completo el ecosistema de reservas de espacios de trabajo compartido. La plataforma permite administrar en tiempo real oficinas privadas, aulas de capacitación y salas de reuniones dinámicas, garantizando una experiencia fluida, intuitiva y segura para todos los miembros.

Desarrollada bajo rigurosos estándares de arquitectura de software en Laravel, la aplicación integra un robusto control de acceso basado en múltiples roles, autenticación segura de terceros (Google OAuth), un sistema automatizado de notificaciones transaccionales por correo electrónico y un panel de control analítico administrativo enfocado en la toma de decisiones y la eficiencia operativa de espacios de coworking reales.

</div>

---

## Características del Sistema

| Módulo | Descripción |
| :--- | :--- |
| **Autenticación** | Registro, inicio de sesión, verificación de correo electrónico y recuperación de contraseña mediante tokens de expiración temporal. |
| **Google OAuth** | Integración nativa con Google a través de Laravel Socialite, incluyendo un flujo dinámico para completar el perfil del usuario en su primer registro. |
| **Control de Acceso** | Arquitectura de roles estructurada (Super-Admin, Administrador y Cliente) protegida por middlewares específicos. |
| **Gestión de Espacios** | Administración completa (CRUD) de oficinas, aulas y salas de reuniones, con control de disponibilidad y estados. |
| **Motor de Reservas** | Búsqueda inteligente por disponibilidad horaria, proceso de confirmación/cancelación y tareas programadas para la expiración de reservas. |
| **Panel de Control** | Dashboard administrativo con métricas clave, control de reservas, gestión de personal, copias de seguridad del sistema y logs. |
| **Calificaciones** | Sistema de feedback que permite a los clientes valorar y dejar comentarios sobre los espacios utilizados una vez concluida su reserva. |
| **Notificaciones** | Envío automatizado de correos electrónicos transaccionales para cada evento clave en el ciclo de vida de la reserva. |

---

## Stack Tecnológico

*   **Backend:** PHP 8.2+ y Laravel 12
*   **Frontend:** Blade Templates y CSS nativo estructurado
*   **Base de Datos:** MySQL 8.0
*   **Autenticación Externa:** Google OAuth 2.0 (Laravel Socialite)
*   **Autenticación Local:** Laravel Breeze
*   **Servicio de Correo:** Gmail SMTP con cifrado TLS (Puerto 587)
*   **Sesiones y Caché:** Driver de base de datos para máxima consistencia

---

## Instalación y Despliegue Rápido

> [!IMPORTANT]
> Asegúrese de contar con **PHP 8.2+**, **Composer** y **MySQL** instalados y configurados en su entorno global antes de proceder.

Ejecute la siguiente secuencia de comandos en su terminal para inicializar el proyecto:

```bash
# 1. Clonar el repositorio
git clone https://github.com/Yei-Yei-idk/Colabs
cd Colabs

# 2. Configurar el archivo de entorno
copy .env.example .env

# 3. Instalar las dependencias de PHP
composer install

# 4. Generar la clave única de la aplicación
php artisan key:generate

# 5. Ejecutar las migraciones y seeders
php artisan migrate:fresh --seed

# 6. Iniciar el servidor local de desarrollo
php artisan serve
```

La aplicación estará operativa de inmediato en: `http://127.0.0.1:8000`


---

## Modelo de Datos y Relaciones

El esquema de la base de datos está diseñado para mantener la integridad referencial del flujo de reservas:

```
usuarios          ── (User)         id, nombre, correo, google_id, telefono, rol_id, estado
espacios          ── (Espacio)      espacio_id, nombre, descripcion, capacidad, tipo, precio_hora, activo
reservas          ── (Reserva)      reserva_id, user_id, espacio_id, fecha, hora_inicio, hora_fin, estado
calificaciones    ── (Calificacion) user_id, espacio_id, reserva_id, puntuacion, comentario, fecha
imagenes          ── (Imagen)       imagen_id, espacio_id, ruta_archivo
roles             ── (Rol)          rol_id, nombre_rol
```


---

## Correos Transaccionales Implementados

| Evento del Sistema | Canal | Propósito |
| :--- | :--- | :--- |
| **Registro de Usuario** | `BienvenidaCuentaCreadaMail` | Envío de bienvenida con credenciales y guías de inicio al cliente. |
| **Verificación de Email** | `VerifyEmailCustom` | Enlace temporal firmado para validar la dirección de correo (Expira en 1 hora). |
| **Recuperación de Acceso**| `RestablecerContrasenaMail` | Enlace seguro y temporal para redefinir la clave de acceso local. |
| **Cambios en Reservas**   | `ReservaStatusChanged` | Notificación inmediata al cliente si su reserva pasa a Aceptada, Rechazada o Finalizada. |
| **Alta de Personal**      | `NuevoAdminRegistradoMail` | Notificación del sistema al Super-Admin cuando se da de alta un nuevo administrador. |

---

## Control de Roles y Permisos

| Nivel de Rol | Identificador (`rol_id`) | Alcance y Permisos |
| :--- | :---: | :--- |
| **Super-Admin** | `1` | Acceso global irrestricto, configuración del sistema y administración de cuentas de personal administrativo. |
| **Administrador** | `2` | Gestión y control del catálogo de espacios, auditoría de reservas activas y descarga de copias de seguridad del sistema. |
| **Cliente** | `3` | Búsqueda de disponibilidad, creación de reservas, visualización del historial propio y calificación de espacios consumidos. |

> [!NOTE]
> **Usuarios con Google Sign-In:** Al autenticarse por primera vez mediante Google, la cuenta se crea de forma automática bajo el rol de **Cliente**. Para garantizar la seguridad del servicio, el middleware `VerificarPerfilGoogleCompleto` redirige al usuario a completar su número telefónico y documento de identidad antes de permitirle realizar reservas en la plataforma.

---

## Estado del Proyecto y Proyección Comercial

Este sistema fue concebido inicialmente en un entorno estrictamente académico con el fin de validar metodologías de desarrollo, seguridad y arquitectura de software en Laravel. 

No obstante, debido a su viabilidad técnica, alto nivel de personalización y desempeño, **el equipo de desarrollo se encuentra actualmente en diálogos y negociaciones activas con la administración de Co-Labs** para evaluar su despliegue comercial en el mercado real, proyectando una fase piloto para convertirlo en la plataforma oficial de gestión y reservas de la empresa.

---

<div align="center">

Desarrollado para la optimización de espacios de coworking en **Co-Labs** • 2026

</div>