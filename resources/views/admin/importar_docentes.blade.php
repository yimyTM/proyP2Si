@extends('layouts.app')

@section('title', 'Carga Masiva de Personal')
@section('page-title', 'CU02 – Carga Masiva de Personal Docente')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Instrucciones --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
        <h3 class="font-semibold text-blue-800 mb-2">Instrucciones</h3>
        <ol class="list-decimal list-inside space-y-1 text-sm text-blue-700">
            <li>Descargue la plantilla CSV haciendo clic en el botón de abajo.</li>
            <li>Complete los datos de los docentes (un docente por fila).</li>
            <li>Suba el archivo con el botón "Importar".</li>
            <li>El sistema creará los registros y generará contraseñas provisionales para quienes tengan correo.</li>
        </ol>
        <a href="{{ route('admin.importar-personal.plantilla') }}"
           class="inline-flex items-center gap-2 mt-3 px-4 py-2 text-sm rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Descargar plantilla CSV
        </a>
    </div>

    {{-- Columnas esperadas --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Columnas del CSV</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 border-b">
                        <th class="pb-2 pr-4">Columna</th>
                        <th class="pb-2 pr-4">Requerida</th>
                        <th class="pb-2">Descripción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach([
                        ['nombre',      'Sí',  'Nombre(s) del docente'],
                        ['apellido',    'Sí',  'Apellido(s) del docente'],
                        ['ci',          'Sí',  'Cédula de identidad (única)'],
                        ['correo',      'No',  'Correo institucional (genera cuenta de acceso)'],
                        ['nroTelefono', 'No',  'Teléfono de contacto'],
                        ['direccion',   'No',  'Dirección domiciliaria'],
                    ] as $col)
                    <tr>
                        <td class="py-2 pr-4 font-mono text-xs text-gray-700">{{ $col[0] }}</td>
                        <td class="py-2 pr-4">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $col[1] === 'Sí' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $col[1] }}
                            </span>
                        </td>
                        <td class="py-2 text-gray-600">{{ $col[2] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Formulario de carga --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Subir archivo CSV</h3>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                @foreach($errors->all() as $e)
                    <p class="text-red-600 text-sm">{{ $e }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.importar-personal.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo CSV</label>
                <div id="drop-zone"
                     class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-[#283342] transition">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">Arrastra tu CSV aquí o <span class="text-[#283342] font-medium underline">haz clic para seleccionar</span></p>
                    <p id="nombre-archivo" class="text-xs text-gray-400 mt-2">Ningún archivo seleccionado</p>
                    <input type="file" id="archivo" name="archivo" accept=".csv,text/csv" class="hidden">
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-lg text-white font-semibold text-sm transition hover:opacity-90"
                    style="background-color: #283342;">
                Importar Personal
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const zone  = document.getElementById('drop-zone');
    const input = document.getElementById('archivo');
    const label = document.getElementById('nombre-archivo');

    zone.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
        label.textContent = input.files[0]?.name ?? 'Ningún archivo seleccionado';
    });
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-[#283342]'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('border-[#283342]'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        input.files = e.dataTransfer.files;
        label.textContent = input.files[0]?.name ?? 'Ningún archivo seleccionado';
        zone.classList.remove('border-[#283342]');
    });
</script>
@endpush
@endsection
