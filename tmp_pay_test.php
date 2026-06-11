<?php

use App\Models\Docente;
use App\Models\requisito;
use Illuminate\Support\Facades\DB;

echo "=== Config Stripe ===" . PHP_EOL;
echo 'secret set: ' . (config('services.stripe.secret') ? 'sí (' . substr(config('services.stripe.secret'), 0, 8) . '...)' : 'NO') . PHP_EOL;
echo 'monto: ' . config('services.stripe.monto') . ' ' . config('services.stripe.currency') . PHP_EOL;

echo PHP_EOL . "=== Stripe Checkout Session (test API) ===" . PHP_EOL;
try {
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    $session = \Stripe\Checkout\Session::create([
        'mode' => 'payment',
        'line_items' => [[
            'quantity' => 1,
            'price_data' => [
                'currency' => config('services.stripe.currency'),
                'unit_amount' => (int) config('services.stripe.monto'),
                'product_data' => ['name' => 'Inscripción FICCT (prueba)'],
            ],
        ]],
        'success_url' => 'http://localhost:8000/registro/pago/exito?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => 'http://localhost:8000/registro/pago/cancelado',
    ]);
    echo 'OK session id: ' . $session->id . PHP_EOL;
    echo 'payment_status: ' . $session->payment_status . PHP_EOL;
    echo 'url empieza con: ' . substr($session->url, 0, 35) . '...' . PHP_EOL;
} catch (\Throwable $e) {
    echo 'ERROR Stripe: ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== Flujo postulación docente (rollback) ===" . PHP_EOL;
DB::beginTransaction();
try {
    $d = Docente::create(['nombre' => 'Test', 'apellido' => 'Postulante', 'ci' => 'POSTDOC' . rand(100, 999), 'nroTelefono' => '700']);
    $d->formAcademicas()->sync([1, 2]);
    foreach (requisito::where('tipo', 'D')->pluck('idReq') as $idReq) {
        $d->requisitosDocente()->updateOrCreate(['idReq' => $idReq], ['entregado' => true, 'validado' => false, 'fecha_entrega' => now()->toDateString()]);
    }
    $d->refresh();
    echo 'Docente creado codigoDoc=' . $d->codigoDoc . ' idUsuario=' . var_export($d->idUsuario, true) . ' (sin cuenta = correcto)' . PHP_EOL;
    echo 'Formaciones: ' . $d->formAcademicas()->count() . PHP_EOL;
    echo 'Requisitos entregados: ' . $d->requisitosDocente()->where('entregado', true)->count() . ' (validados: ' . $d->requisitosDocente()->where('validado', true)->count() . ')' . PHP_EOL;
    DB::rollBack();
    echo 'rollback aplicado.' . PHP_EOL;
} catch (\Throwable $e) {
    DB::rollBack();
    echo 'ERROR docente: ' . $e->getMessage() . PHP_EOL;
}
