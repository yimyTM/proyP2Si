@extends('layouts.app')

@section('title', 'Postulante – Dashboard')
@section('page-title', 'Panel del Postulante')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="md:col-span-2 rounded-xl p-6 text-white" style="background-color: #283342;">
        <h2 class="text-xl font-bold mb-1">
            Bienvenido, {{ Auth::user()->nombreCompleto }}
        </h2>
        <p class="text-white/70 text-sm">
            Has iniciado sesión como <strong>Postulante</strong>.
            Desde aquí gestionarás tu proceso de admisión a la FICCT.
        </p>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Accesos rápidos</p>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{ route('verificar-pago') }}"
                   class="flex items-center gap-2 text-[#283342] hover:underline font-medium">
                    <span class="w-2 h-2 rounded-full bg-[#283342]"></span>
                    CU03 – Verificar estado de pago
                </a>
            </li>
            <li>
                <a href="{{ route('postulante.expediente') }}"
                   class="flex items-center gap-2 text-[#283342] hover:underline font-medium">
                    <span class="w-2 h-2 rounded-full bg-[#283342]"></span>
                    CU05 – Registrar expediente digital
                </a>
            </li>
        </ul>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Datos de sesión</p>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Rol:</span>
                <span class="font-medium text-gray-800">{{ Auth::user()->rol?->nombre_Rol }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Correo:</span>
                <span class="font-medium text-gray-800 truncate ml-2">{{ Auth::user()->correo }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
