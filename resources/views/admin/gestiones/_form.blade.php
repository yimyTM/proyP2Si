@php
    $gestion = $gestion ?? null;
@endphp

@if($errors->any())
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Nombre --}}
    <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre de la gestión *</label>
        <input type="text" name="nombre"
               value="{{ old('nombre', $gestion?->nombre) }}"
               placeholder="Ej: Gestión 2025-I"
               maxlength="100"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                      {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : '' }}">
    </div>

    {{-- Fecha inicio --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha de inicio *</label>
        <input type="date" name="fecha_ini"
               value="{{ old('fecha_ini', $gestion?->fecha_ini?->format('Y-m-d')) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                      {{ $errors->has('fecha_ini') ? 'border-red-400 bg-red-50' : '' }}"
               id="fecha_ini">
    </div>

    {{-- Fecha fin --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha de fin *</label>
        <input type="date" name="fecha_fin"
               value="{{ old('fecha_fin', $gestion?->fecha_fin?->format('Y-m-d')) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                      {{ $errors->has('fecha_fin') ? 'border-red-400 bg-red-50' : '' }}"
               id="fecha_fin">
        <p id="fecha_error" class="text-xs text-red-500 mt-1 hidden">La fecha de fin debe ser posterior a la de inicio.</p>
    </div>

    {{-- Capacidad máxima --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Capacidad máxima *</label>
        <input type="number" name="capacidad_maxima"
               value="{{ old('capacidad_maxima', $gestion?->capacidad_maxima) }}"
               min="1" placeholder="Ej: 120"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                      {{ $errors->has('capacidad_maxima') ? 'border-red-400 bg-red-50' : '' }}">
        <p class="text-xs text-gray-400 mt-1">Total de postulantes que puede admitir la gestión.</p>
    </div>

    {{-- Estado (solo en edición) --}}
    @if($gestion)
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
        <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="Cerrada" {{ old('estado', $gestion->estado) === 'Cerrada' ? 'selected' : '' }}>Cerrada</option>
            <option value="Abierta" {{ old('estado', $gestion->estado) === 'Abierta' ? 'selected' : '' }}>Abierta</option>
        </select>
        <p class="text-xs text-gray-400 mt-1">Solo puede haber una gestión abierta a la vez.</p>
    </div>
    @endif

</div>

<p class="text-xs text-gray-500 mt-3">
    Los cupos por carrera se configuran después, en <strong>Cupos por carrera</strong>.
</p>

@push('scripts')
<script>
    // Validación en tiempo real: fecha_fin >= fecha_ini
    const ini = document.getElementById('fecha_ini');
    const fin = document.getElementById('fecha_fin');
    const err = document.getElementById('fecha_error');

    function validarFechas() {
        if (ini.value && fin.value && fin.value < ini.value) {
            err.classList.remove('hidden');
            fin.classList.add('border-red-400', 'bg-red-50');
        } else {
            err.classList.add('hidden');
            fin.classList.remove('border-red-400', 'bg-red-50');
        }
    }

    ini.addEventListener('change', validarFechas);
    fin.addEventListener('change', validarFechas);
</script>
@endpush
