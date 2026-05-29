@php
    $docente = $docente ?? null;
    $isEdit = isset($docente);
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
        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $docente?->nombre) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Apellido *</label>
        <input type="text" name="apellido" value="{{ old('apellido', $docente?->apellido) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Cédula de Identidad *</label>
        <input type="text" name="ci" value="{{ old('ci', $docente?->ci) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Correo electrónico</label>
        <input type="email" name="correo" value="{{ old('correo', $docente?->correo) }}"
               placeholder="Si se indica, se crea cuenta de acceso"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
        <input type="text" name="nroTelefono" value="{{ old('nroTelefono', $docente?->nroTelefono) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Carga horaria (hrs)</label>
        <input type="number" name="carga_horaria" value="{{ old('carga_horaria', $docente?->carga_horaria) }}"
               min="0" max="40"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-600 mb-1">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $docente?->direccion) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
</div>
