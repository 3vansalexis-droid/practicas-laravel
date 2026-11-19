<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de verificación</title>
</head>
<body style="font-family: Arial, sans-serif; color: #212529; line-height: 1.5;">
    <h2>Hola, {{ $nombreUsuario }}</h2>
    <p>Se ha solicitado un inicio de sesión como <strong>Administrador</strong> en el Sistema Modular de Prácticas.</p>
    <p>Tu código de verificación de 6 dígitos es:</p>
    <p style="font-size: 32px; font-weight: bold; letter-spacing: 6px; background: #f8f9fa; padding: 12px 20px; display: inline-block; border: 1px solid #dee2e6; border-radius: 6px;">
        {{ $codigo }}
    </p>
    <p>Este código <strong>expira en 5 minutos</strong>. Si no realizaste esta solicitud, puedes ignorar este correo.</p>
</body>
</html>
