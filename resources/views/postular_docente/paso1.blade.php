<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Postulación Docente – Paso 1 | FICCT</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <a href="{{ route('home') }}" class="font-bold text-[#001e40] text-lg">FICCT Portal</a>
    <span class="text-sm text-gray-500">Postulación Docente</span>
</nav>

{{-- Wizard --}}
<div class="max-w-3xl mx-auto px-4 pt-8 pb-4">
    <div class="flex items-center gap-0">
        @foreach([['1','Datos y formación',false],['2','Documentos',false]] as [$n,$label,$done])
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $loop->first ? 'bg-[#001e40] text-white' : 'bg-gray-200 text-gray-500' }}">{{ $n }}</div>
                <span class="text-sm font-medium {{ $loop->first ? 'text-[#001e40]' : 'text-gray-400' }} hidden sm:block">{{ $label }}</span>
            </div>
            @if(!$loop->last)<div class="flex-1 h-0.5 mx-3 bg-gray-200"></div>@endif
        </div>
        @endforeach
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #001e40 0%, #003366 100%);">
            <h1 class="text-xl font-bold text-white">Postulación como Docente</h1>
            <p class="text-white/60 text-sm mt-1">Regístrate como candidato. El FICCT revisará tus documentos y, de ser seleccionado, te contactará.</p>
        </div>

        <form method="POST" action="{{ route('postular-docente.store') }}" class="p-6 space-y-6">
            @csrf

            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Datos personales --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-3">Datos personales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#001e40]/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Apellido *</label>
                        <input type="text" name="apellido" value="{{ old('apellido') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#001e40]/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cédula de Identidad *</label>
                        <input type="text" name="ci" value="{{ old('ci') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#001e40]/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
                        <input type="text" name="nroTelefono" value="{{ old('nroTelefono') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#001e40]/20">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#001e40]/20">
                    </div>
                </div>
            </div>

            {{-- Formación académica --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Formación académica</h3>
                <p class="text-xs text-gray-400 mb-3">Selecciona tus títulos o agrega nuevos (licenciatura/ingeniería, maestría, diplomado).</p>

                @if($formaciones->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                    @foreach($formaciones as $f)
                    <label class="flex items-start gap-2 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="formaciones[]" value="{{ $f->idForm }}"
                               class="mt-0.5 w-4 h-4 rounded border-gray-300" style="accent-color:#001e40;">
                        <span class="text-sm text-gray-700 leading-snug">
                            {{ $f->nombProfesion }}
                            @if($f->nroProfesion)<span class="block text-xs text-gray-400">{{ $f->nroProfesion }}</span>@endif
                        </span>
                    </label>
                    @endforeach
                </div>
                @endif

                <div id="nuevas-profesiones" class="space-y-2"></div>
                <button type="button" onclick="agregarProfesion()"
                        class="mt-2 text-xs px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                    + Agregar otra profesión
                </button>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-white text-sm font-semibold transition hover:opacity-90"
                        style="background-color: #001e40;">
                    Continuar a documentos →
                </button>
                <a href="{{ route('home') }}"
                   class="px-6 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let profIdx = 0;
function agregarProfesion() {
    const cont = document.getElementById('nuevas-profesiones');
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center';
    row.innerHTML = `
        <input type="text" name="nuevas_profesiones[${profIdx}][nombProfesion]" placeholder="Nombre de la profesión (ej. Maestría en...)"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#001e40]/20">
        <input type="text" name="nuevas_profesiones[${profIdx}][nroProfesion]" placeholder="N° registro (opcional)"
               class="w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#001e40]/20">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm px-2">✕</button>
    `;
    cont.appendChild(row);
    profIdx++;
}
</script>
</body>
</html>
