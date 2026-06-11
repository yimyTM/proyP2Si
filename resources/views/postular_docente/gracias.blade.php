<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Postulación enviada | FICCT</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <a href="{{ route('home') }}" class="font-bold text-[#001e40] text-lg">FICCT Portal</a>
    <span class="text-sm text-gray-500">Postulación Docente</span>
</nav>

<div class="max-w-xl mx-auto px-4 py-16">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-8 flex flex-col items-center text-center gap-6">
            <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">¡Postulación enviada!</h1>
                <p class="text-gray-500 mt-2 max-w-md leading-relaxed">
                    Hemos recibido tu postulación como docente. El FICCT revisará y validará tus documentos.
                    Si eres seleccionado y <strong>contratado</strong>, se creará tu cuenta de acceso y te notificaremos por correo.
                </p>
            </div>
            <a href="{{ route('home') }}"
               class="w-full text-center px-6 py-3 bg-[#001e40] hover:bg-[#003366] text-white font-semibold rounded-lg transition text-sm">
                Volver al inicio
            </a>
        </div>
    </div>
</div>

</body>
</html>
