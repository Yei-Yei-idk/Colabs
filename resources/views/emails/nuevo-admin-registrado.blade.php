<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - Colabs</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f2; font-family:'Inter','Segoe UI',Roboto,Arial,sans-serif; -webkit-font-smoothing:antialiased;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f2; padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="680" cellpadding="0" cellspacing="0" style="width:100%; max-width:680px; background-color:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 12px 40px rgba(0,0,0,0.08);">

                    <tr>
                        <td style="background:#000000; padding:32px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <img src="{{ $message->embed(public_path('ASSETS/logo.png')) }}" alt="Colabs Logo" height="36" style="display:block; border:none;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background: linear-gradient(90deg, #facc15, #eab308); height:6px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="padding:56px 60px 40px; text-align:center;">
                            <div style="width:64px; height:64px; border-radius:16px; background:rgba(250,204,21,0.15); display:inline-block; text-align:center; line-height:64px; font-size:28px; margin-bottom:24px;">
                                🛡️
                            </div>

                            <h1 style="margin:0 0 16px; color:#111827; font-size:32px; font-weight:800; letter-spacing:-1px; line-height:1.1;">
                                Acceso Administrativo
                            </h1>

                            <p style="color:#4b5563; font-size:16px; line-height:1.7; margin:0 0 28px; max-width:500px; margin-left:auto; margin-right:auto;">
                                Hola, <strong style="color:#111827;">{{ $usuario->user_nombre }}</strong>. Se ha creado una cuenta administrativa para ti en
                                <span style="font-weight:700; color:#eab308;">Colabs</span>.
                                A continuación encontrarás tus credenciales de acceso.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="background:#f8fafc; border-radius:16px; border:1px solid #e2e8f0; max-width:480px; width:100%;">
                                            <tr>
                                                <td style="padding:24px; text-align:left;">
                                                    <p style="margin:0 0 8px; font-size:13px; color:#64748b; text-transform:uppercase; letter-spacing:1px;">Correo electrónico</p>
                                                    <p style="margin:0 0 20px; font-size:16px; color:#111827; font-weight:600;">{{ $usuario->user_correo }}</p>
                                                    
                                                    <p style="margin:0 0 8px; font-size:13px; color:#64748b; text-transform:uppercase; letter-spacing:1px;">Contraseña temporal</p>
                                                    <p style="margin:0; font-size:16px; color:#111827; font-weight:600; font-family: 'Courier New', monospace; background: #eaeff5; padding: 10px; border-radius: 8px;">{{ $password }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="color:#6b7280; font-size:14px; margin-bottom:28px;">
                                Te recomendamos cambiar tu contraseña una vez hayas ingresado por primera vez.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('login') }}"
                                           style="display:inline-block; background:linear-gradient(90deg,#facc15,#eab308); color:#000000; text-decoration:none; padding:18px 48px; border-radius:12px; font-size:16px; font-weight:800; letter-spacing:0.5px; box-shadow:0 12px 24px rgba(234,179,8,0.3);">
                                            Iniciar Sesión
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#000000; padding:32px 40px; text-align:center;">
                            <p style="color:#64748b; font-size:12px; margin:0 0 8px;">
                                &copy; {{ date('Y') }} <span style="color:#facc15; font-weight:700;">Colabs</span>.
                                Gestión administrativa inteligente.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
