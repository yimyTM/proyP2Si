<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICCT – Verificar Pago</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center" style="background-color: #283342;">

<div class="w-full max-w-md px-6 py-10">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-white/10 flex items-center justify-center border border-white/20">
            <span class="text-white font-bold text-xl">FC</span>
        </div>
        <h1 class="text-white text-xl font-bold">FICCT – Verificación de Pago</h1>
        <p class="text-white/50 text-xs mt-1">Paso 1 de 2 – Consulta tu estado de pago</p>
    </div>

    {{-- Resultado: credenciales creadas --}}
    @isset($credenciales)
    <div class="bg-white rounded-2xl p-6 shadow-2xl mb-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-xl">✓</div>
            <div>
                <p class="font-bold text-gray-800">¡Pago verificado!</p>
                <p class="text-xs text-gray-400">Tu cuenta ha sido creada</p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
            <p class="text-xs font-semibold text-amber-800 mb-2">⚠ Guarda estas credenciales ahora — no se volverán a mostrar</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Correo:</span>
                    <code class="font-mono text-gray-800">{{ $credenciales['correo'] }}</code>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Contraseña:</span>
                    <code class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded">{{ $credenciales['password'] }}</code>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-500 mb-4">
            Tu pago de <strong>Bs. {{ number_format($pago->monto, 2) }}</strong>
            del {{ $pago->fecha->format('d/m/Y') }} está registrado y aprobado.
        </p>

        <a href="{{ route('login') }}"
           class="block w-full py-2.5 text-center rounded-lg text-white text-sm font-semibold transition hover:opacity-90"
           style="background-color: #283342;">
            Ir al inicio de sesión
        </a>
    </div>
    @endisset

    {{-- Formulario de verificación --}}
    @empty($credenciales)
    <div class="bg-white rounded-2xl p-6 shadow-2xl">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Consultar estado de pago</h2>
        <p class="text-gray-400 text-sm mb-5">Ingresa tu Cédula de Identidad para verificar si tu pago está aprobado.</p>

        {{-- Alertas --}}
        @if(session('warning'))
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 text-sm">
                {{ session('warning') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                @foreach($errors->all() as $e)
                    <p class="text-red-600 text-sm">{{ $e }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('postulante.verificar-pago.verificar') }}">
            @csrf
            <div class="mb-4">
                <label for="ci" class="block text-sm font-medium text-gray-700 mb-1">Número de CI</label>
                <input type="text" id="ci" name="ci" value="{{ old('ci') }}"
                       placeholder="Ej: 12345678"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            </div>
            <button type="submit"
                    class="w-full py-3 rounded-lg text-white font-semibold text-sm transition hover:opacity-90"
                    style="background-color: #283342;">
                Verificar pago
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-4">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-[#283342] hover:underline font-medium">Inicia sesión aquí</a>
        </p>
    </div>
    @endempty

</div>
</body>
</html>
