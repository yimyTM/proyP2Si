<?php

use App\Http\Middleware\NoCacheHeaders;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

$mw = new NoCacheHeaders();
$req = Request::create('/admin/reportes/export', 'GET');

// 1) StreamedResponse (caso del CSV)
$streamed = response()->streamDownload(fn () => print('a,b,c'), 'test.csv');
$out = $mw->handle($req, fn () => $streamed);
echo 'StreamedResponse → Cache-Control: ' . $out->headers->get('Cache-Control') . PHP_EOL;
echo 'Tipo: ' . get_class($out) . PHP_EOL;

// 2) Response normal (caso del PDF de dompdf / vistas)
$normal = response('contenido');
$out2 = $mw->handle($req, fn () => $normal);
echo PHP_EOL . 'Response → Cache-Control: ' . $out2->headers->get('Cache-Control') . PHP_EOL;

echo PHP_EOL . 'OK — el middleware ya no llama withHeaders().' . PHP_EOL;
