<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\GestionCarreraController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\DocenteGrupoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\RequisitoPostulanteController;
use App\Http\Controllers\RequisitoController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AsistenciaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Raíz ──────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Ruta /dashboard: resuelve el destino según el rol del usuario ─────────────
// Esta ruta es el destino del middleware 'guest' cuando el usuario ya está
// autenticado, evitando el bucle infinito con /login.
Route::middleware('auth')->get('/dashboard', function () {
    return match (Auth::user()->rol?->nombre_Rol) {
        'Administrador' => redirect()->route('admin.dashboard'),
        'Docente'       => redirect()->route('docente.dashboard'),
        'Postulante'    => redirect()->route('postulante.dashboard'),
        default         => redirect('/'),
    };
})->name('dashboard');

// ── CU01: Autenticación ───────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
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
Route::middleware(['auth', 'role:Administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // CU01 – Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // CU02 – Carga masiva de personal
        Route::get('/importar-personal',           [DocenteController::class, 'importar'])->name('importar-personal');
        Route::post('/importar-personal',          [DocenteController::class, 'importarStore'])->name('importar-personal.store');
        Route::get('/importar-personal/plantilla', [DocenteController::class, 'plantilla'])->name('importar-personal.plantilla');

        // CRUD Docentes
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

        // CU04 (Admin) – Supervisión de expedientes
        Route::get('/expedientes', [RequisitoPostulanteController::class, 'supervisar'])->name('expedientes');
        Route::post('/expedientes/postulante/{postulante}/{requisito}', [RequisitoPostulanteController::class, 'estadoPostulante'])->name('expedientes.postulante.estado');
        Route::post('/expedientes/docente/{docente}/{requisito}',       [RequisitoPostulanteController::class, 'estadoDocente'])->name('expedientes.docente.estado');

        // CU05 – Búsqueda y gestión de postulantes
        Route::get('/estudiantes', [PostulanteController::class, 'buscar'])->name('estudiantes');
        Route::resource('postulantes', PostulanteController::class)->except(['index']);

        // CRUD Grupos (manual)
        Route::resource('grupos', GrupoController::class)
            ->parameters(['grupos' => 'grupo'])
            ->except(['show']);

        // CU10 – Asignación docente y carga logística
        Route::get('/asignacion-docente',  [DocenteGrupoController::class, 'asignacion'])->name('asignacion-docente');
        Route::post('/asignacion-docente', [DocenteGrupoController::class, 'store'])->name('asignacion-docente.store');

        // Roles y Permisos
        Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
        Route::put('/roles/{rol}/permisos', [RolController::class, 'actualizarPermisos'])->name('roles.permisos.update');

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

        // Asistencia
        Route::get('/asistencia',            [AsistenciaController::class, 'index'])->name('asistencia.index');
        Route::get('/asistencia/{grupo}',    [AsistenciaController::class, 'tomar'])->name('asistencia.tomar');
        Route::post('/asistencia',           [AsistenciaController::class, 'store'])->name('asistencia.store');
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
    });
