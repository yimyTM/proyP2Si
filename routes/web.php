<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\GestionCarreraController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\Admin\AsignacionDocenteController;
use App\Http\Controllers\Admin\AdmisionController;
use App\Http\Controllers\Admin\ImportPostulanteController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\PostulacionDocenteController;
use App\Http\Controllers\SolicitudMateriaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MateriGrupoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\RequisitoPostulanteController;
use App\Http\Controllers\RequisitoController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\Docente\CalificacionController;
use App\Http\Controllers\Docente\ResultadoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Raíz / Landing pública ────────────────────────────────────────────────────
Route::get('/', [RegistroController::class, 'landing'])->name('home');

// ── Registro de postulante – Paso 1 (solo invitados) ──────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/registro',  [RegistroController::class, 'index'])->name('registro');
    Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store');
});

// ── Postulación pública de docentes (sin cuenta ni pago) ──────────────────────
Route::get('/postular-docente',             [PostulacionDocenteController::class, 'index'])->name('postular-docente');
Route::post('/postular-docente',            [PostulacionDocenteController::class, 'store'])->name('postular-docente.store');
Route::get('/postular-docente/documentos',  [PostulacionDocenteController::class, 'documentos'])->name('postular-docente.documentos');
Route::post('/postular-docente/documentos', [PostulacionDocenteController::class, 'storeDocumentos'])->name('postular-docente.documentos.store');
Route::get('/postular-docente/gracias',     [PostulacionDocenteController::class, 'gracias'])->name('postular-docente.gracias');

// ── Registro de postulante – Pasos 2 y 3 (postulante autenticado) ─────────────
Route::middleware(['auth', 'role:Postulante'])->group(function () {
    Route::get('/registro/documentos',  [RegistroController::class, 'showDocumentos'])->name('registro.documentos');
    Route::post('/registro/documentos', [RegistroController::class, 'storeDocumentos'])->name('registro.documentos.store');

    // Paso 3 – Pago con Stripe Checkout
    Route::get('/registro/pago',           [RegistroController::class, 'showPago'])->name('registro.pago');
    Route::post('/registro/pago/checkout', [RegistroController::class, 'crearCheckout'])->name('registro.pago.checkout');
    Route::get('/registro/pago/exito',     [RegistroController::class, 'pagoExito'])->name('registro.pago.exito');
    Route::get('/registro/pago/cancelado', [RegistroController::class, 'pagoCancelado'])->name('registro.pago.cancelado');
});

// ── Ruta /dashboard: resuelve el destino según el rol del usuario ─────────────
// Esta ruta es el destino del middleware 'guest' cuando el usuario ya está
// autenticado, evitando el bucle infinito con /login.
Route::middleware('auth')->get('/dashboard', function () {
    return match (Auth::user()->rol?->nombre_Rol) {
        'Administrador', 'Autoridades', 'Coordinador' => redirect()->route('admin.dashboard'),
        'Docente'    => redirect()->route('docente.dashboard'),
        'Postulante' => redirect()->route('postulante.dashboard'),
        default      => abort(403, 'Rol sin panel asignado.'),
    };
})->name('dashboard');

// ── CU01: Autenticación ───────────────────────────────────────────────────────
// nocache evita que el navegador cachee la página de login con un token CSRF viejo
Route::middleware(['guest', 'nocache'])->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/password/olvidada', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/olvidada', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/restablecer/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/restablecer', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── CU03: Verificación de pago (público, sin autenticación) ──────────────────
Route::get('/verificar-pago',  [PagoController::class, 'verificar'])->name('verificar-pago');
Route::post('/verificar-pago', [PagoController::class, 'consultar'])->name('postulante.verificar-pago.verificar');

// ── Panel Administrador ───────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Administrador,Autoridades,Coordinador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // CU01 – Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // CU02 – Carga masiva de personal
        Route::get('/importar-personal',           [DocenteController::class, 'importar'])->name('importar-personal');
        Route::post('/importar-personal',          [DocenteController::class, 'importarStore'])->name('importar-personal.store');
        Route::get('/importar-personal/plantilla', [DocenteController::class, 'plantilla'])->name('importar-personal.plantilla');

        // Carga masiva de postulantes + cálculo/creación automática de grupos
        Route::get('/importar-postulantes',           [ImportPostulanteController::class, 'form'])->name('importar-postulantes');
        Route::post('/importar-postulantes',          [ImportPostulanteController::class, 'procesar'])->name('importar-postulantes.procesar');
        Route::post('/importar-postulantes/confirmar',[ImportPostulanteController::class, 'confirmar'])->name('importar-postulantes.confirmar');
        Route::get('/importar-postulantes/plantilla', [ImportPostulanteController::class, 'plantilla'])->name('importar-postulantes.plantilla');

        // CRUD Docentes
        Route::post('/docentes/{docente}/contratar', [DocenteController::class, 'contratar'])->name('docentes.contratar');
        Route::post('/docentes/{docente}/provisionar-cuenta', [DocenteController::class, 'provisionarCuenta'])->name('docentes.provisionar-cuenta');
        Route::resource('docentes', DocenteController::class);

        // Gestiones académicas
        Route::patch('/gestiones/{gestion}/abrir',  [GestionController::class, 'abrir'])->name('gestiones.abrir');
        Route::patch('/gestiones/{gestion}/cerrar', [GestionController::class, 'cerrar'])->name('gestiones.cerrar');
        Route::get('/gestiones/{gestion}/carreras',  [GestionCarreraController::class, 'index'])->name('gestiones.carreras.index');
        Route::put('/gestiones/{gestion}/carreras',  [GestionCarreraController::class, 'update'])->name('gestiones.carreras.update');
        Route::resource('gestiones', GestionController::class)
            ->except(['show'])
            ->parameters(['gestiones' => 'gestion']);

        // CU05 (Admin) – Supervisión y validación de expedientes
        Route::get('/expedientes', [RequisitoPostulanteController::class, 'supervisar'])->name('expedientes');
        Route::post('/expedientes/postulante/{postulante}/estado-masivo', [RequisitoPostulanteController::class, 'estadoMasivoPostulante'])->name('expedientes.postulante.estado.masivo');
        Route::post('/expedientes/postulante/{postulante}/{requisito}', [RequisitoPostulanteController::class, 'estadoPostulante'])->name('expedientes.postulante.estado');
        Route::post('/expedientes/docente/{docente}/{requisito}',       [RequisitoPostulanteController::class, 'estadoDocente'])->name('expedientes.docente.estado');
        Route::patch('/expedientes/{inscripcion}/validar',  [InscripcionController::class, 'validarAdmin'])->name('expedientes.validar');
        Route::patch('/expedientes/{inscripcion}/rechazar', [InscripcionController::class, 'rechazarAdmin'])->name('expedientes.rechazar');

        // CU05 – Búsqueda y gestión de postulantes
        Route::get('/estudiantes', [PostulanteController::class, 'buscar'])->name('estudiantes');
        Route::resource('postulantes', PostulanteController::class)->except(['index']);

        // CU03 – CRUD Aulas
        Route::resource('aulas', AulaController::class)
            ->parameters(['aulas' => 'aula']);

        // CU06 – CRUD Grupos (manual)
        Route::get('/grupos/distribuir',  [GrupoController::class, 'distribuirPreview'])->name('grupos.distribuir');
        Route::post('/grupos/distribuir', [GrupoController::class, 'distribuirConfirmar'])->name('grupos.distribuir.confirmar');
        Route::resource('grupos', GrupoController::class)
            ->parameters(['grupos' => 'grupo'])
            ->except(['show']);

        // CU07 – Asignación logística (materia/horario/aula/docente por grupo)
        Route::post('/grupos/{grupo}/materias',             [MateriGrupoController::class, 'store'])->name('grupos.materias.store');
        Route::delete('/grupos/{grupo}/materias/{materia}', [MateriGrupoController::class, 'destroy'])->name('grupos.materias.destroy');

        // Asignación docente (vista global admin)
        Route::get('/asignacion-docente',  [AsignacionDocenteController::class, 'index'])->name('asignacion-docente');
        Route::post('/asignacion-docente', [AsignacionDocenteController::class, 'store'])->name('asignacion-docente.store');

        // Roles y Permisos
        Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
        Route::put('/roles/{rol}/permisos', [RolController::class, 'actualizarPermisos'])->name('roles.permisos.update');

        // Solicitudes de materia (Admin y Coordinador) — permiso ver_solicitudes
        Route::middleware('role:Administrador,Coordinador')->group(function () {
            Route::get('/solicitudes', [SolicitudMateriaController::class, 'index'])->name('solicitudes.index');
            Route::post('/solicitudes/{codigoDoc}/{idMateria}/aceptar',  [SolicitudMateriaController::class, 'aceptar'])->name('solicitudes.aceptar');
            Route::post('/solicitudes/{codigoDoc}/{idMateria}/rechazar', [SolicitudMateriaController::class, 'rechazar'])->name('solicitudes.rechazar');
        });

        // CU14 – Reportes y analíticas institucionales (panel con filtros)
        Route::get('/reportes',        [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');
        Route::get('/reportes/pdf',    [ReporteController::class, 'pdf'])->name('reportes.pdf');

        // CU13 – Admisión y cupos por carrera
        Route::get('/admision',           [AdmisionController::class, 'index'])->name('admision.index');
        Route::get('/admision/{gestion}',  [AdmisionController::class, 'show'])->name('admision.show');
        Route::post('/admision/{gestion}', [AdmisionController::class, 'procesar'])->name('admision.procesar');

        // CRUD Turnos
        Route::get('/turnos',              [TurnoController::class, 'index'])->name('turnos.index');
        Route::post('/turnos',             [TurnoController::class, 'store'])->name('turnos.store');
        Route::put('/turnos/{turno}',      [TurnoController::class, 'update'])->name('turnos.update');
        Route::delete('/turnos/{turno}',   [TurnoController::class, 'destroy'])->name('turnos.destroy');

        // CRUD Requisitos
        Route::get('/requisitos',                    [RequisitoController::class, 'index'])->name('requisitos.index');
        Route::post('/requisitos',                   [RequisitoController::class, 'store'])->name('requisitos.store');
        Route::put('/requisitos/{requisito}',        [RequisitoController::class, 'update'])->name('requisitos.update');
        Route::delete('/requisitos/{requisito}',     [RequisitoController::class, 'destroy'])->name('requisitos.destroy');
    });

// ── Panel Docente ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {
        Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('dashboard');

        // Solicitud de materias que desea dictar
        Route::get('/solicitud-materias',  [SolicitudMateriaController::class, 'misSolicitudes'])->name('solicitud-materias');
        Route::post('/solicitud-materias', [SolicitudMateriaController::class, 'solicitar'])->name('solicitud-materias.store');

        // Asistencia
        Route::get('/asistencia',            [AsistenciaController::class, 'index'])->name('asistencia.index');
        Route::get('/asistencia/{grupo}',    [AsistenciaController::class, 'tomar'])->name('asistencia.tomar');
        Route::post('/asistencia',           [AsistenciaController::class, 'store'])->name('asistencia.store');

        // CU11 – Calificaciones
        Route::get('/calificaciones',                     [CalificacionController::class, 'index'])->name('calificaciones.index');
        Route::get('/calificaciones/{grupo}/{materia}',   [CalificacionController::class, 'edit'])->name('calificaciones.edit');
        Route::post('/calificaciones/{grupo}/{materia}',  [CalificacionController::class, 'update'])->name('calificaciones.update');

        // CU12 – Resultados
        Route::get('/resultados',          [ResultadoController::class, 'index'])->name('resultados.index');
        Route::get('/resultados/{grupo}',  [ResultadoController::class, 'show'])->name('resultados.show');
        Route::post('/resultados/{grupo}', [ResultadoController::class, 'procesar'])->name('resultados.procesar');
    });

// ── Panel Postulante ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Postulante'])
    ->prefix('postulante')
    ->name('postulante.')
    ->group(function () {
        // CU01 – Dashboard
        Route::get('/dashboard', [PostulanteController::class, 'dashboard'])->name('dashboard');

        // CU04 – Expediente digital
        Route::get('/expediente',  [InscripcionController::class, 'expediente'])->name('expediente');
        Route::post('/expediente', [InscripcionController::class, 'guardarExpediente'])->name('expediente.store');

        // CU16 – Resultados de admisión y calificaciones
        Route::get('/resultados', [PostulanteController::class, 'resultados'])->name('resultados');
    });
