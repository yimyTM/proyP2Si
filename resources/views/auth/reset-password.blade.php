<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICCT – Nueva contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl items-center justify-center text-white font-bold text-xl mb-4" style="background-color: #283342;">FC</div>
            <h1 class="text-2xl font-bold text-gray-800">Nueva contraseña</h1>
            <p class="text-sm text-gray-500 mt-2">Elige una contraseña segura para tu cuenta.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" value="{{ old('correo', $correo) }}" required readonly
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-600">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/20">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/20">
                </div>

                @include('partials.password-strength', ['inputId' => 'password'])

                <button type="submit"
                        class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90"
                        style="background-color: #283342;">
                    Guardar nueva contraseña
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: #283342;">← Volver al inicio de sesión</a>
            </p>
        </div>
    </div>
</body>
</html>
