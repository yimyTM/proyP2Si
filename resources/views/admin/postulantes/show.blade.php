@extends('layouts.app')

@section('title', $postulante->nombre_completo)
@section('page-title', 'Detalle del Postulante')

@section('content')
<div class="space-y-5 max-w-4xl">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ $postulante->nombre_completo }}</h2>
            <p class="text-sm text-gray-500">CI: {{ $postulante->ci }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.postulantes.edit', $postulante) }}"
               class="px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
               style="background-color: #283342;">
                Editar
            </a>
            <a href="{{ route('admin.estudiantes') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                Volver al listado
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Información personal</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Correo</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->correo ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Teléfono</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->nroTelefono ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Sexo</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->sexo === 'M' ? 'Masculino' : ($postulante->sexo === 'F' ? 'Femenino' : '—') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Estado</dt>
                <dd class="font-medium text-gray-800">{{ ucfirst($postulante->estado) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Fecha de nacimiento</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Ciudad</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->ciudad ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Colegio de procedencia</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->colegio_procedencia ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Dirección</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->direccion ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Cuenta de usuario</dt>
                <dd class="font-medium text-gray-800">
                    @if($postulante->usuario)
                        <span class="text-green-700">Activa</span> — {{ $postulante->usuario->correo }}
                    @else
                        <span class="text-gray-500">Sin cuenta (agregue un correo para crearla)</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-3">Pagos</h3>
            @if($postulante->pagos->isEmpty())
                <p class="text-sm text-gray-400">Sin pagos registrados.</p>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach($postulante->pagos as $pago)
                    <li class="flex justify-between">
                        <span>{{ $pago->fecha?->format('d/m/Y') ?? '—' }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $pago->estado === 'aprobado' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ ucfirst($pago->estado) }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-3">Expediente</h3>
            @if($postulante->requisitos->isEmpty())
                <p class="text-sm text-gray-400">Sin documentos entregados.</p>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach($postulante->requisitos as $req)
                    <li class="flex justify-between items-center">
                        <span>{{ $req->requisito?->nombre ?? 'Documento' }}</span>
                        @if($req->validado)
                            <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Validado</span>
                        @elseif($req->entregado)
                            <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">En revisión</span>
                        @else
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Pendiente</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @if($postulante->inscripciones->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Carreras inscritas</h3>
        </div>
        <div class="px-6 py-4">
            @foreach($postulante->inscripciones as $inscripcion)
                <div class="mb-3 last:mb-0">
                    @forelse($inscripcion->carrerasInscritas->sortBy('prioridad') as $ci)
                        <span class="inline-block text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full mr-1 mb-1">
                            Op.{{ $ci->prioridad }}: {{ $ci->carrera?->nombre ?? '—' }}
                        </span>
                    @empty
                        <span class="text-sm text-gray-400">Sin carreras en esta inscripción</span>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.postulantes.destroy', $postulante) }}"
          onsubmit="return confirm('¿Eliminar permanentemente a {{ $postulante->nombre_completo }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-600 hover:underline">
            Eliminar postulante
        </button>
    </form>

</div>
@endsection
