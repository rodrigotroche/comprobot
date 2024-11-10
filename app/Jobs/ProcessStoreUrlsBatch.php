<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\StoreUrl;
use Illuminate\Support\Facades\Log;

class ProcessStoreUrlsBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batchSize;

    /**
     * Create a new job instance.
     *
     * @param int $batchSize
     */
    public function __construct($batchSize = 10)
    {
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Obtener un lote limitado de URLs con status 'pending'
        $storeUrls = StoreUrl::where('status', 'pending')->limit($this->batchSize)->get();

        foreach ($storeUrls as $storeUrl) {
            // Marcar la URL como 'in_progress'
            $storeUrl->update(['status' => 'in_progress']);

            try {
                // Lógica para procesar la URL (ejecutar un Job de sincronización, etc.)
                Log::info("Procesando URL: {$storeUrl->url}");

                // Simulación de procesamiento (sustituye esto con tu lógica de sincronización)
                sleep(2);  // Simular un tiempo de procesamiento

                // Actualizar el estado a 'processed' al finalizar
                $storeUrl->update(['status' => 'processed']);
                Log::info("URL procesada con éxito: {$storeUrl->url}");
            } catch (\Exception $e) {
                // Registrar error y actualizar el estado a 'error'
                Log::error("Error al procesar la URL {$storeUrl->url}: " . $e->getMessage());
                $storeUrl->update(['status' => 'error']);
            }
        }
    }
}
