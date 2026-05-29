@extends('layouts.app')

@section('title', 'Resultado de Importación')
@section('page-title', 'CU02 – Resultado de Carga Masiva')

@section('content')
<div class="space-y-6">

    {{-- Resumen --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-green-700">{{ count($exitosos) }}</p>
            <p class="text-sm text-green-600 mt-1">Docentes importados</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-red-700">{{ count($errores) }}</p>
            <p class="text-sm text-red-600 mt-1">Filas con errores</p>
        </div>
    </div>

    {{-- Tabla de resultados exitosos --}}
    @if(count($exitosos) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Cuentas generadas</h3>
            <span class="text-xs text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                ⚠ Guarde estas contraseñas — no se volverán a mostrar
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">CI</th>
                        <th class="px-6 py-3">Correo</th>
                        <th class="px-6 py-3">Contraseña provisional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($exitosos as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $d['nombre'] }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $d['ci'] }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $d['correo'] }}</td>
                        <td class="px-6 py-3">
                            <code class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-800">
                                {{ $d['password'] }}
                            </code>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Errores --}}
    @if(count($errores) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Filas no procesadas</h3>
        <ul class="space-y-1">
            @foreach($errores as $err)
                <li class="flex items-start gap-2 text-sm text-red-600">
                    <span class="mt-0.5">✗</span> {{ $err }}
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    <a href="{{ route('admin.importar-personal') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white transition hover:opacity-90"
       style="background-color: #283342;">
        ← Importar otro archivo
    </a>
</div>
@endsection
