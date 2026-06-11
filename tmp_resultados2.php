<?php

use App\Models\User;
use App\Http\Controllers\PostulanteController;
use Illuminate\Support\Facades\Auth;

$user = User::where('correo', 'dflores@estudiante.bo')->first();
Auth::login($user);

$view = app(PostulanteController::class)->resultados();
$data = $view->getData();

echo 'Vista: ' . $view->name() . PHP_EOL;
if (isset($data['error'])) {
    echo 'ERROR branch: ' . $data['error'] . PHP_EOL;
} else {
    echo 'Materias con notas: ' . $data['materias']->count() . PHP_EOL;
    echo 'Exámenes: ' . $data['examenes']->count() . PHP_EOL;
    foreach ($data['materias'] as $m) {
        $notas = $m->parciales->map(fn ($n) => $n->calificacion . '/' . ($n->examMateria->puntaje))->implode(', ');
        echo '   ' . str_pad($m->nombre, 14) . ' -> ' . $notas . PHP_EOL;
    }
    // Render real de la vista para detectar errores Blade
    $html = $view->render();
    echo PHP_EOL . 'Vista renderizada OK: ' . strlen($html) . ' bytes' . PHP_EOL;
}
