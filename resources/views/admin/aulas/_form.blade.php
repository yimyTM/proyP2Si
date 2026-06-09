<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Capacidad máxima <span class="text-red-500">*</span>
    </label>
    <input type="number" name="capacidad" min="1" max="999"
           value="{{ old('capacidad', $edicion ? $aula->capacidad : '') }}"
           placeholder="Ej: 30, 40, 70"
           class="w-full max-w-xs px-3 py-2.5 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                  {{ $errors->has('capacidad') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
    @error('capacidad')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    <p class="text-xs text-gray-400 mt-1">Número máximo de alumnos que puede albergar el aula.</p>
</div>
