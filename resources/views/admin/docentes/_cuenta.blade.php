{{-- Acción rápida: restablecer contraseña (solo si ya tiene cuenta).
     La creación de cuenta se hace desde la vista de detalle (requiere correo + contrato). --}}
@if($docente->usuario)
<form method="POST" action="{{ route('admin.docentes.provisionar-cuenta', $docente) }}" class="inline"
      onsubmit="return confirm('¿Generar una nueva contraseña para este docente?')">
    @csrf
    <button type="submit" class="text-xs text-indigo-600 hover:underline">
        Restablecer contraseña
    </button>
</form>
@endif
