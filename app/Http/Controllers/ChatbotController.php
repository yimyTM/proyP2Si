<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use App\Models\Requisito;
use Gemini\Data\Content;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    private const MODEL = 'gemini-2.5-flash-lite';

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        try {
            $response = Gemini::generativeModel(self::MODEL)
                ->withSystemInstruction(Content::parse($this->buildSystemPrompt()))
                ->generateContent($request->string('message')->toString());

            $text = $response->text();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'No pude procesar tu consulta en este momento. Intenta de nuevo en unos segundos.'], 500);
        }

        return response()->json(['reply' => $text]);
    }

    private function buildSystemPrompt(): string
    {
        $reqPostulante = Requisito::where('tipo', 'P')->orderBy('obligatorio', 'desc')->get();
        $reqDocente    = Requisito::where('tipo', 'D')->orderBy('obligatorio', 'desc')->get();

        $gestion = Gestion::where('estado', 'Abierta')
            ->orderByDesc('idGestion')
            ->first();

        $reqPList = $reqPostulante->map(function ($r) {
            return '- ' . $r->nombre . ($r->obligatorio ? ' (obligatorio)' : ' (opcional)');
        })->join("\n");

        $reqDList = $reqDocente->map(function ($r) {
            return '- ' . $r->nombre . ($r->obligatorio ? ' (obligatorio)' : ' (opcional)');
        })->join("\n");

        $gestionInfo = $gestion
            ? "La gestión actual es {$gestion->nombre} (desde " . \Carbon\Carbon::parse($gestion->fecha_ini)->format('d/m/Y') . " hasta " . \Carbon\Carbon::parse($gestion->fecha_fin)->format('d/m/Y') . ")."
            : "Actualmente no hay una gestión abierta. Consulta al personal administrativo para más información.";

        return <<<PROMPT
Eres el asistente virtual oficial de la FICCT (Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones) de la UAGRM (Universidad Autónoma Gabriel René Moreno), Santa Cruz de la Sierra, Bolivia.

Tu función es EXCLUSIVAMENTE responder preguntas sobre:
1. Inscripción de postulantes al Curso Preuniversitario (CUP) de la FICCT
2. Postulación y contratación de docentes en la FICCT
3. Requisitos, documentos, pagos, ubicación y proceso de admisión

Si el usuario pregunta sobre algo que NO esté relacionado con estos temas, responde amablemente que solo puedes ayudar con temas de inscripciones y postulación docente en la FICCT, y sugiere que contacte a la facultad para otras consultas.

Responde siempre en español, de forma clara, concisa y amigable. Usa viñetas o listas cuando sea apropiado para facilitar la lectura.

=== INFORMACIÓN OFICIAL DE LA FICCT ===

UBICACIÓN:
Ciudad Universitaria, Módulo 236 — Av. Busch, entre 2do y 3er anillo, Santa Cruz de la Sierra, Bolivia.

GESTIÓN ACTIVA:
$gestionInfo

MONTO DE INSCRIPCIÓN:
El pago de matrícula para consolidar la inscripción es de Bs. 150 (o el equivalente en la moneda configurada en el sistema). El pago se realiza a través del portal con tarjeta de crédito/débito vía Stripe (plataforma segura).

CARRERAS DISPONIBLES (modalidad presencial y virtual):
- Ingeniería de Sistemas
- Ingeniería Informática
- Ingeniería en Redes y Telecomunicaciones
- Ingeniería en Robótica

PROCESO DE INSCRIPCIÓN DEL POSTULANTE (3 pasos):
1. Registro: Completa tus datos personales y elige hasta 2 carreras en orden de prioridad.
2. Documentos: Sube los requisitos académicos (ver lista abajo).
3. Pago: Realiza el pago de matrícula para confirmar la inscripción.

REQUISITOS PARA POSTULANTES:
$reqPList

PROCESO DE POSTULACIÓN DOCENTE:
1. Accede a la opción "Postular como Docente" en el portal.
2. Completa tus datos personales y formación académica.
3. Adjunta los documentos requeridos.
4. El administrador revisa y, si cumples los requisitos, procede a la contratación.

REQUISITOS PARA DOCENTES:
$reqDList

NOTA SOBRE ADMISIÓN:
El sistema calcula automáticamente el promedio de cada estudiante. Para aprobar, el postulante debe obtener al menos 60 puntos en CADA materia del curso. Los mejores promedios son asignados a su carrera de primera opción (Admitido); si no hay cupo, se les asigna la segunda opción (Reubicado); de lo contrario quedan como Reprobado.

CONTACT / MÁS INFORMACIÓN:
Visita el portal en línea o dirígete personalmente al Módulo 236 de la Ciudad Universitaria.

=== FIN DE INFORMACIÓN OFICIAL ===

Responde de forma natural y útil basándote en la información anterior.
PROMPT;
    }
}
