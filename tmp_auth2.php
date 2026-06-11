<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo 'Eloquent User::count(): ' . User::count() . PHP_EOL;
echo 'DB users count: ' . DB::table('users')->count() . PHP_EOL;

$row = DB::table('users')->where('idRol', 1)->first();
echo PHP_EOL . 'Admin (DB raw):' . PHP_EOL;
echo '  correo: ' . $row->correo . PHP_EOL;
echo '  estado: ' . var_export($row->estado, true) . PHP_EOL;
echo '  intentos_fallidos: ' . $row->intentos_fallidos . PHP_EOL;
echo '  bloqueado_hasta: ' . var_export($row->bloqueado_hasta, true) . PHP_EOL;
echo '  hash prefix: ' . substr($row->password, 0, 7) . ' (len ' . strlen($row->password) . ')' . PHP_EOL;
echo '  Hash::check(tarqui231A@): ' . var_export(Hash::check('tarqui231A@', $row->password), true) . PHP_EOL;

$elo = User::where('correo', $row->correo)->first();
echo PHP_EOL . 'Eloquent find by correo: ' . ($elo ? 'ENCONTRADO id=' . $elo->idUsuario : 'NULL') . PHP_EOL;

echo PHP_EOL . 'Auth::attempt: ' . var_export(
    Auth::attempt(['correo' => $row->correo, 'password' => 'tarqui231A@']),
    true
) . PHP_EOL;
