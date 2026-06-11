<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Pago confirmado | FICCT</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <span class="font-bold text-[#001e40] text-lg">FICCT Portal</span>
    <span class="text-sm text-gray-500">{{ Auth::user()->nombreCompleto }}</span>
</nav>

<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-8 flex flex-col items-center text-center gap-6">

            <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-800">¡Pago confirmado!</h1>
                <p class="text-gray-500 mt-2 max-w-md leading-relaxed">
                    Tu inscripción ha sido <strong class="text-green-600">registrada y habilitada</strong> correctamente.
                </p>
            </div>

            <div class="w-full bg-gray-50 rounded-xl border border-gray-200 p-5 text-left space-y-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Comprobante</p>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Postulante</span>
                    <span class="font-semibold text-gray-800">{{ $postulante->nombre }} {{ $postulante->apellidos }}</span>
                </div>
                @if($pago)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">N° de pago</span>
                    <span class="font-semibold text-gray-800">#{{ $pago->nroPago }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Monto pagado</span>
                    <span class="font-bold text-green-700">{{ number_format($pago->monto, 2) }} {{ strtoupper(config('services.stripe.currency')) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Estado inscripción</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-green-100 text-green-700 px-2.5 py-1 rounded-full">
                        Habilitada
                    </span>
                </div>
            </div>

            <a href="{{ route('postulante.dashboard') }}"
               class="w-full text-center px-6 py-3 bg-[#001e40] hover:bg-[#003366] text-white font-semibold rounded-lg transition text-sm">
                Ir a mi Panel
            </a>
        </div>
    </div>
</div>

</body>
</html>
