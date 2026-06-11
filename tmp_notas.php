<?php

use App\Models\Postulante;
use App\Models\User;

echo "=== Estados de pago existentes ===" . PHP_EOL;
foreach (\DB::table('pagos')->get() as $p) {
    echo "  pago #{$p->nroPago} idPost={$p->idPost} estado='{$p->estado}' monto={$p->monto}" . PHP_EOL;
}

echo PHP_EOL . "=== Postulantes con cuenta: inscripción / pago / notas ===" . PHP_EOL;
foreach (Postulante::with('usuario')->get() as $post) {
    $insc = $post->inscripciones()->latest('idInscripcion')->first();
    $notas = $insc ? \DB::table('notas')->where('idInscripcion', $insc->idInscripcion)->count() : 0;
    echo '  ' . str_pad($post->nombre_completo, 26)
        . ' user=' . ($post->usuario?->correo ?? 'SIN CUENTA')
        . ' | inscripcion=' . ($insc?->idInscripcion ?? '—')
        . ' estado=' . ($insc?->estado ?? '—')
        . ' | pagoAprobado=' . var_export($post->tienePagoAprobado(), true)
        . ' | notas=' . $notas
        . PHP_EOL;
}
