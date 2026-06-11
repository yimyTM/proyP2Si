<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Postulación Docente – Documentos | FICCT</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <a href="{{ route('home') }}" class="font-bold text-[#001e40] text-lg">FICCT Portal</a>
    <span class="text-sm text-gray-500">{{ $docente->nombre_completo }}</span>
</nav>

{{-- Wizard --}}
<div class="max-w-3xl mx-auto px-4 pt-8 pb-4">
    <div class="flex items-center gap-0">
        @foreach([['1','Datos y formación',true],['2','Documentos',false]] as [$n,$label,$done])
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $done ? 'bg-green-500 text-white' : 'bg-[#001e40] text-white' }}">
                    @if($done)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else {{ $n }} @endif
                </div>
                <span class="text-sm font-medium {{ $done ? 'text-green-600' : 'text-[#001e40]' }} hidden sm:block">{{ $label }}</span>
            </div>
            @if(!$loop->last)<div class="flex-1 h-0.5 mx-3 {{ $done ? 'bg-green-400' : 'bg-gray-200' }}"></div>@endif
        </div>
        @endforeach
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #001e40 0%, #003366 100%);">
            <h1 class="text-xl font-bold text-white">Documentos habilitantes</h1>
            <p class="text-white/60 text-sm mt-1">Sube tus requisitos en PDF o imagen. Serán validados por el FICCT.</p>
        </div>

        <form method="POST" action="{{ route('postular-docente.documentos.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            @if($requisitos->isEmpty())
                <p class="text-sm text-gray-400">No hay requisitos definidos.</p>
            @else
            <div class="space-y-3">
                @foreach($requisitos as $req)
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-xl border border-gray-100">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">
                            {{ $req->nombre }}
                            @if($req->obligatorio)<span class="text-red-500">*</span>@endif
                        </p>
                    </div>
                    <input type="file" name="archivo_{{ $req->idReq }}" accept=".pdf,.jpg,.jpeg,.png"
                           class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#001e40] file:text-white hover:file:bg-[#003366]">
                </div>
                @endforeach
            </div>
            @endif

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-white text-sm font-semibold transition hover:opacity-90"
                        style="background-color: #001e40;">
                    Enviar postulación
                </button>
                <a href="{{ route('postular-docente') }}"
                   class="px-6 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Volver
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
