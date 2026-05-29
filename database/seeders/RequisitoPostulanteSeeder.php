<?php

namespace Database\Seeders;

use App\Models\Postulante;
use App\Models\requisito;
use App\Models\Requisito_Postulante;
use Illuminate\Database\Seeder;

class RequisitoPostulanteSeeder extends Seeder
{
    public function run(): void
    {
        // Solo los requisitos para postulantes (tipo 'P')
        $requisitosPostulante = requisito::where('tipo', 'P')->get();

        if ($requisitosPostulante->isEmpty()) {
            $this->command->warn('No hay requisitos de tipo E. Ejecuta RequisitoSeeder primero.');
            return;
        }

        $postulantes = Postulante::all();

        if ($postulantes->isEmpty()) {
            $this->command->warn('No hay postulantes. Ejecuta PostulanteSeeder primero.');
            return;
        }

        // Carlos Mamani: tiene todos los documentos y ya validados
        $this->asignarRequisitos(
            $postulantes->firstWhere('ci', '8523641'),
            $requisitosPostulante,
            entregado: true,
            validado: true,
            fecha: '2025-02-10'
        );

        // Lucía Condori: tiene todos los documentos pero pendientes de validación
        $this->asignarRequisitos(
            $postulantes->firstWhere('ci', '9134782'),
            $requisitosPostulante,
            entregado: true,
            validado: false,
            fecha: '2025-02-15'
        );

        // Diego Ticona: solo entregó título de bachiller y cédula, falta certificado de notas
        $parciales = $requisitosPostulante->whereIn('nombre', ['Título de bachiller', 'Cédula de identidad']);
        $this->asignarRequisitos(
            $postulantes->firstWhere('ci', '7841236'),
            $parciales,
            entregado: true,
            validado: false,
            fecha: '2025-02-20'
        );

        // Valeria Gutierrez: no ha entregado ningún documento aún (no se insertan registros)

        // Rodrigo Apaza: solo registrado como entregado sin validar, con cédula
        $soloCI = $requisitosPostulante->where('nombre', 'Cédula de identidad');
        $this->asignarRequisitos(
            $postulantes->firstWhere('ci', '5219634'),
            $soloCI,
            entregado: true,
            validado: false,
            fecha: '2025-03-01'
        );
    }

    private function asignarRequisitos(
        ?Postulante $postulante,
        iterable $requisitos,
        bool $entregado,
        bool $validado,
        string $fecha
    ): void {
        if (! $postulante) {
            return;
        }

        foreach ($requisitos as $req) {
            Requisito_Postulante::firstOrCreate(
                [
                    'idReq'  => $req->idReq,
                    'idPost' => $postulante->idPost,
                ],
                [
                    'fecha_entrega' => $fecha,
                    'entregado'     => $entregado,
                    'validado'      => $validado,
                ]
            );
        }
    }
}
