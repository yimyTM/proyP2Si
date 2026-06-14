{{-- Partial: formulario de grupo (reutilizado en create y edit) --}}
@php
    $edicion = isset($grupo);
    $old = fn(string $k, $def = null) => old($k, $edicion ? $grupo->$k ?? $def : $def);
@endphp

@if($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
        @foreach($errors->all() as $e)
            <p class="text-red-600 text-sm">• {{ $e }}</p>
        @endforeach
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Número de grupo --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Número de grupo <span class="text-red-500">*</span>
        </label>
        <input type="text" name="numero_grupo" maxlength="50"
               value="{{ old('numero_grupo', $edicion ? $grupo->numero_grupo : '') }}"
               placeholder="Ej: A, B, 01, G1..."
               class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                      {{ $errors->has('numero_grupo') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        @error('numero_grupo')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Capacidad --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Capacidad máxima <span class="text-red-500">*</span>
        </label>
        <input type="number" name="capacidad" min="1" max="500"
               value="{{ $old('capacidad', '') }}"
               placeholder="Ej: 40"
               class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                      {{ $errors->has('capacidad') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        @error('capacidad')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Modalidad --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Modalidad <span class="text-red-500">*</span>
        </label>
        <select name="codeModalidad"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">Seleccione...</option>
            @foreach($modalidades as $m)
                <option value="{{ $m->codeModalidad }}"
                    {{ old('codeModalidad', $edicion ? $grupo->codeModalidad : '') == $m->codeModalidad ? 'selected' : '' }}>
                    {{ $m->nombModalidad }}
                </option>
            @endforeach
        </select>
    </div>


</div>

<div class="flex gap-3 mt-6">
    <button type="submit"
            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
            style="background-color: #283342;">
        {{ $edicion ? 'Actualizar Grupo' : 'Crear Grupo' }}
    </button>
    <a href="{{ route('admin.grupos.index') }}"
       class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
        Cancelar
    </a>
</div>
