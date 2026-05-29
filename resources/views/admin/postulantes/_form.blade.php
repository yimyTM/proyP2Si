@php
    $postulante = $postulante ?? null;
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
        <input type="text" name="nombre" value="{{ old('nombre', $postulante?->nombre) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Apellidos *</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $postulante?->apellidos) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Cédula de Identidad *</label>
        <input type="text" name="ci" value="{{ old('ci', $postulante?->ci) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Correo electrónico</label>
        <input type="email" name="correo" value="{{ old('correo', $postulante?->correo) }}"
               placeholder="Si se indica, se crea cuenta de acceso"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
        <input type="text" name="nroTelefono" value="{{ old('nroTelefono', $postulante?->nroTelefono) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Sexo</label>
        <select name="sexo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">—</option>
            <option value="M" {{ old('sexo', $postulante?->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo', $postulante?->sexo) === 'F' ? 'selected' : '' }}>Femenino</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Estado *</label>
        <select name="estado" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="activo" {{ old('estado', $postulante?->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estado', $postulante?->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', $postulante?->fecha_nacimiento?->format('Y-m-d')) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Ciudad</label>
        <input type="text" name="ciudad" value="{{ old('ciudad', $postulante?->ciudad) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Colegio de procedencia</label>
        <input type="text" name="colegio_procedencia" value="{{ old('colegio_procedencia', $postulante?->colegio_procedencia) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-600 mb-1">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $postulante?->direccion) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>
</div>
