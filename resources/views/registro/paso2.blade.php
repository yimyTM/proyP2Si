<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Inscripción – Paso 2 | FICCT</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script>
    tailwind.config = {
        theme: { extend: {
            colors: { 'ficct': '#001e40', 'ficct-c': '#003366' },
            fontFamily: { sans: ['Inter','sans-serif'] }
        }}
    }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .drop-zone {
        border: 2px dashed #d1d5db;
        transition: border-color 0.2s, background 0.2s;
    }
    .drop-zone:hover, .drop-zone.drag-over {
        border-color: #001e40;
        background: #f0f4f8;
    }
    .drop-zone.has-file {
        border-color: #16a34a;
        background: #f0fdf4;
    }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Navbar --}}
<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <span class="font-bold text-[#001e40] text-lg">FICCT Portal</span>
    <span class="text-sm text-gray-500">{{ Auth::user()->nombreCompleto }}</span>
</nav>

{{-- Wizard steps --}}
<div class="max-w-3xl mx-auto px-4 pt-8 pb-4">
    <div class="flex items-center gap-0">
        @foreach([['1','Datos personales',false,true],['2','Documentos',true,false],['3','Pago',false,false]] as [$n,$label,$active,$done])
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $done ? 'bg-green-500 text-white' : ($active ? 'bg-[#001e40] text-white' : 'bg-gray-200 text-gray-400') }}">
                    @if($done)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    @else
                    {{ $n }}
                    @endif
                </div>
                <span class="text-sm font-medium {{ $active ? 'text-[#001e40]' : ($done ? 'text-green-600' : 'text-gray-400') }} hidden sm:block">{{ $label }}</span>
            </div>
            @if(!$loop->last)
            <div class="flex-1 h-0.5 mx-3 {{ $done ? 'bg-green-400' : 'bg-gray-200' }}"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Formulario --}}
<div class="max-w-3xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #001e40 0%, #003366 100%);">
            <h1 class="text-xl font-bold text-white">Subida de documentos</h1>
            <p class="text-white/60 text-sm mt-1">
                Sube tus requisitos en formato imagen (JPG, PNG) o PDF. Máx. 5 MB por archivo.
            </p>
        </div>

        <form method="POST" action="{{ route('registro.documentos.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
            @endif

            @foreach($requisitos as $req)
            @php
                $campo    = 'archivo_' . $req->idReq;
                $yaSubido = isset($yaSubidos[$req->idReq]);
            @endphp
            <div>
                <div class="flex items-start justify-between mb-2">
                    <label for="{{ $campo }}" class="block text-sm font-semibold text-gray-800">
                        {{ $req->nombre }}
                        @if(!$req->obligatorio)
                        <span class="ml-1 text-xs font-normal text-gray-400">(opcional)</span>
                        @else
                        <span class="text-red-500 ml-0.5">*</span>
                        @endif
                    </label>
                    @if($yaSubido)
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Ya subido
                    </span>
                    @endif
                </div>

                <label id="zone_{{ $req->idReq }}"
                       for="{{ $campo }}"
                       class="drop-zone {{ $yaSubido ? 'has-file' : '' }} rounded-xl p-5 flex flex-col items-center justify-center cursor-pointer gap-2 text-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">
                        @if($yaSubido)
                            Haz clic para <strong>reemplazar</strong> el archivo
                        @else
                            Arrastra o <strong class="text-[#001e40]">selecciona</strong> el archivo
                        @endif
                    </p>
                    <p id="name_{{ $req->idReq }}" class="text-xs text-gray-400">PDF, JPG, PNG — máx. 5 MB</p>
                    <input type="file"
                           id="{{ $campo }}"
                           name="{{ $campo }}"
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="hidden"
                           onchange="updateZone({{ $req->idReq }}, this)"/>
                </label>
            </div>
            @endforeach

            <div class="border-t border-gray-100 pt-5 flex items-center justify-between">
                <a href="{{ route('postulante.dashboard') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 transition">
                    Omitir por ahora →
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-[#001e40] hover:bg-[#003366] text-white font-semibold rounded-lg transition shadow-sm text-sm">
                    Continuar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateZone(id, input) {
    const zone = document.getElementById('zone_' + id);
    const label = document.getElementById('name_' + id);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeMB = (file.size / 1048576).toFixed(1);
        zone.classList.add('has-file');
        label.textContent = file.name + ' (' + sizeMB + ' MB)';
        label.classList.remove('text-gray-400');
        label.classList.add('text-green-700', 'font-medium');
    }
}

// Drag & drop visual feedback
document.querySelectorAll('.drop-zone').forEach(zone => {
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag-over'); });
});
</script>
</body>
</html>
