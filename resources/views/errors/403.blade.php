<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICCT – Acceso denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center" style="background-color: #283342;">
    <div class="text-center text-white">
        <p class="text-6xl font-bold mb-4">403</p>
        <p class="text-xl mb-2">Acceso denegado</p>
        <p class="text-white/60 text-sm mb-8">{{ $exception->getMessage() ?: 'No tienes permiso para acceder a esta sección.' }}</p>
        <a href="{{ url('/') }}" class="px-6 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition">
            Volver al inicio
        </a>
    </div>
</body>
</html>
