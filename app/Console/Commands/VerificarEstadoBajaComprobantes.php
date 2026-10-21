<?php

namespace App\Console\Commands;

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BajaComprobante;
use Greenter\See;
use Greenter\Model\Response\StatusCdrResult;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VerificarEstadoBajaComprobantes extends Command
{
    protected $signature = 'comprobantes:verificar-baja';
    protected $description = 'Verificar el estado de los comprobantes en baja pendientes de confirmación en SUNAT';

    public function handle()
    {
        $see = new See();
        $see->setService(SunatEndpoints::FE_BETA); // Cambia a FE_PRODUCCION en producción

        $bajasPendientes = BajaComprobante::where('estado_sunat', 'pendiente')->get();

        foreach ($bajasPendientes as $baja) {
            try {
                $status = $see->getStatus($baja->ticket_baja);
                $error = $status->getError();

                if ($status instanceof StatusCdrResult && $status->isSuccess()) {
                    // Obtener y almacenar el CDR ZIP
                    $cdrZip = $status->getCdrZip();
                    $cdrFilePath = "cdrs/{$baja->ticket_baja}.zip";
                    Storage::put($cdrFilePath, $cdrZip);

                    // Calcular hash y actualizar la base de datos
                    $hash = hash('sha1', $status->getCdrResponse()->getCdrXml());
                    $baja->update([
                        'estado_sunat' => 'aceptada',
                        'hash_cpe' => $hash,
                    ]);

                    Log::info("Baja aceptada para el ticket: {$baja->ticket_baja}. CDR almacenado en: $cdrFilePath");
                } elseif ($status instanceof StatusCdrResult) {
                    // Baja rechazada
                    $error = $status->getError();
                    $baja->update([
                        'estado_sunat' => 'rechazada',
                    ]);

                    Log::error("Baja rechazada para el ticket: {$baja->ticket_baja} - Error: {$error->getMessage()}");
                } else {
                    // Estado indefinido o error de comunicación
                    Log::warning("Estado indefinido para el ticket: {$baja->ticket_baja}. Se intentará nuevamente. - Error: {$error->getMessage()}");
                }
            } catch (\Exception $e) {
                Log::error("Error al verificar el estado de baja para el ticket {$baja->ticket_baja}: " . $e->getMessage());
            }
        }
    }
}
