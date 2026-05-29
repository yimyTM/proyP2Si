@extends('layouts.app')

@section('title', 'Postulantes')
@section('page-title', 'CU05 – Gestión de Postulantes')

@section('content')
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('admin.postulantes.create') }}"
           class="px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
           style="background-color: #283342;">
            + Nuevo postulante
        </a>
    </div>

    {{-- Formulario de búsqueda --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Filtros de búsqueda</h3>
        <form method="GET" action="{{ route('admin.estudiantes') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cédula de Identidad</label>
                <input type="text" name="ci" value="{{ request('ci') }}"
                       placeholder="Ej: 12345678"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Apellido</label>
                <input type="text" name="apellido" value="{{ request('apellido') }}"
                       placeholder="Búsqueda parcial..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Carrera</label>
                <select name="carrera" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                    <option value="">Todas las carreras</option>
                    @foreach($carreras as $c)
                        <option value="{{ $c->codCarrera }}" {{ request('carrera') == $c->codCarrera ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
                <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                    <option value="">Todos</option>
                    <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="md:col-span-4 flex gap-3">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #283342;">
                    Buscar
                </button>
                <a href="{{ route('admin.estudiantes') }}"
                   class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Limpiar filtros
                </a>
            </div>
        </form>
    </div>

    {{-- Resultados --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                Resultados
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $postulantes->total() }} registros)</span>
            </h3>
        </div>

        @if($postulantes->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                @if(request()->anyFilled(['ci', 'apellido', 'carrera', 'estado']))
                    No se encontraron postulantes con los filtros aplicados.
                @else
                    No hay postulantes registrados.
                @endif
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Postulante</th>
                        <th class="px-6 py-3">CI</th>
                        <th class="px-6 py-3">Carrera(s)</th>
                        <th class="px-6 py-3">Pago</th>
                        <th class="px-6 py-3">Expediente</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($postulantes as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $p->nombre }} {{ $p->apellidos }}</p>
                            <p class="text-xs text-gray-400">{{ $p->correo }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $p->ci }}</td>
                        <td class="px-6 py-3">
                            @forelse($p->inscripciones->flatMap->carrerasInscritas->sortBy('prioridad') as $ci)
                                <span class="inline-block text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full mb-0.5">
                                    Op.{{ $ci->prioridad }}: {{ $ci->carrera?->nombre ?? '—' }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400">Sin inscripción</span>
                            @endforelse
                        </td>
                        <td class="px-6 py-3">
                            @if($p->tienePagoAprobado())
                                <span class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">✓ Aprobado</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">⏳ Pendiente</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @php $req = $p->requisitos->first(); @endphp
                            @if($req && $req->validado)
                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">✓ Validado</span>
                            @elseif($req && $req->entregado)
                                <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">En revisión</span>
                            @else
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Sin entregar</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $p->estado === 'activo' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($p->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.postulantes.show', $p) }}"
                                   class="text-xs text-blue-600 hover:underline">Ver</a>
                                <a href="{{ route('admin.postulantes.edit', $p) }}"
                                   class="text-xs text-amber-600 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('admin.postulantes.destroy', $p) }}"
                                      onsubmit="return confirm('¿Eliminar a {{ $p->nombre }} {{ $p->apellidos }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $postulantes->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
