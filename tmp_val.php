<?php

use Illuminate\Support\Facades\Validator;

$correos = ['yimyt771@gmail.com', 'dflores@estudiante.bo', 'jpeña@ficct.edu.bo', 'secretaria@ficct.edu.bo'];

echo "Regla actual  email:rfc,dns" . PHP_EOL;
foreach ($correos as $c) {
    $v = Validator::make(['correo' => $c], ['correo' => ['required', 'email:rfc,dns']]);
    echo '  ' . str_pad($c, 30) . ($v->passes() ? 'PASA' : 'FALLA') . PHP_EOL;
}

echo PHP_EOL . "Regla propuesta  email:rfc" . PHP_EOL;
foreach ($correos as $c) {
    $v = Validator::make(['correo' => $c], ['correo' => ['required', 'email:rfc']]);
    echo '  ' . str_pad($c, 30) . ($v->passes() ? 'PASA' : 'FALLA') . PHP_EOL;
}
