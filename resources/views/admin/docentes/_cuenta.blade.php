@if($docente->correo)
<form method="POST" action="{{ route('admin.docentes.provisionar-cuenta', $docente) }}" class="inline"
      onsubmit="return confirm(@js($docente->usuario ? '¿Generar una nueva contraseña para este docente?' : '¿Crear cuenta de acceso para este docente?'))">
    @csrf
    <button type="submit"
            class="text-xs px-3 py-1 rounded-lg text-white transition hover:opacity-90"
            style="background-color: #283342;">
        {{ $docente->usuario ? 'Restablecer contraseña' : 'Crear cuenta' }}
    </button>
</form>
@endif
