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
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha de inicio *</label>
        <input type="date" name="fecha_ini"
               value="{{ old('fecha_ini', $gestion?->fecha_ini?->format('Y-m-d')) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha de fin *</label>
        <input type="date" name="fecha_fin"
               value="{{ old('fecha_fin', $gestion?->fecha_fin?->format('Y-m-d')) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    @if($gestion)
    <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
        <select name="estado" class="w-full max-w-xs px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="Cerrada" {{ old('estado', $gestion->estado) === 'Cerrada' ? 'selected' : '' }}>Cerrada</option>
            <option value="Abierta" {{ old('estado', $gestion->estado) === 'Abierta' ? 'selected' : '' }}>Abierta</option>
        </select>
        <p class="text-xs text-gray-400 mt-1">Solo puede haber una gestión abierta a la vez.</p>
    </div>
    @endif
</div>

<p class="text-xs text-gray-500 mt-3">
    Los cupos por carrera se configuran después de crear la gestión, en <strong>Cupos por carrera</strong>.
</p>
