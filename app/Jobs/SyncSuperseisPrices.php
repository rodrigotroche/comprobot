<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\StoreUrl;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\TempProduct;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SyncSuperseisPrices implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public $tries = 5;
    public $backoff = 60;

    public function handle(): void
    {
        try {
            ini_set('max_execution_time', 3600);
            ini_set('memory_limit', '2048M');

            Log::info('Iniciando sincronización de Superseis');

            $store = Store::where('name', 'Superseis')->first();

            // Verificar que haya pasado al menos 8 horas desde la última sincronización
            if ($store->last_synced_at && $store->last_synced_at->diffInHours(now()) < 8) {
                Log::info('Aún no han pasado 8 horas desde la última sincronización');
                return;
            }

            if (!$store) {
                Log::error('No se encontró la tienda Superseis');
                return;
            }

            $urls = StoreUrl::where('store_id', $store->id)
                ->where('enabled', true)
                ->where('status', 'pending')
                ->where('type', 'product_list')
                ->limit(2)
                ->get();

            Log::info('Cantidad de URLs encontradas: ' . count($urls));

            foreach ($urls as $key => $storeUrl) {
                if ($key > 0) {
                    Log::info('Esperando 5 segundos antes de continuar');
                }

                $client = HttpClient::create();
                $response = $client->request("GET", $storeUrl->url);

                if ($response->getStatusCode() === 429) {
                    Log::warning('Demasiadas solicitudes, esperando 30 segundos');
                    sleep(30);
                    continue;
                }

                if ($response->getStatusCode() !== 200) {
                    Log::error('Error al obtener la página: ' . $storeUrl->url);
                    return;
                }

                $html = $response->getContent();
                $crawler = new Crawler($html);

                if ($crawler->filter('.product-item')->count() === 0) {
                    Log::error('No se encontraron productos en la página');
                    continue;
                }

                $crawler->filter('.product-item')->each(function (Crawler $node) use ($client, $store, $storeUrl) {
                    Log::info('Procesando producto');

                    // Obtenemos el nombre del producto
                    $name = $node->filter('.product-title a')->text();

                    // Obtenemos el precio y lo formateamos
                    $price = self::formatPrice($node->filter('.productPrice .price-label')->text());

                    // Obtenemos el URL del producto
                    $productUrl = $node->filter('.product-title a')->attr('href');

                    // Extraemos el ID del producto desde la URL
                    $externalId = $this->extractProductIdFromUrl($productUrl);

                    // Obtenemos la URL de la imagen del producto
                    $imageUrl = $node->filter('.picture-link img')->attr('src');
                    $imageContents = $client->request('GET', $imageUrl)->getContent();
                    $imageName = basename($imageUrl);
                    Storage::disk('public')->put('images/' . $imageName, $imageContents);

                    // Guardamos los productos en la tabla temporal `TempProduct`
                    TempProduct::updateOrCreate(
                        [
                            'store_id' => $store->id,
                            'reference_id' => $externalId,
                        ],
                        [
                            'name' => $name,
                            'url' => $productUrl,
                            'image' => $imageName,
                            'current_price' => $price,
                            'status' => 'pending',
                        ]
                    );
                });

                $storeUrl->updated_at = now();
                $storeUrl->status = 'processed';
                $storeUrl->save();
            }

            Log::info('Sincronización de Superseis finalizado');
        } catch (\Exception $e) {
            Log::error('Error durante la sincronización: ' . $e->getMessage());
            throw $e;
        }
    }

    public function extractProductIdFromUrl(string $productUrl): ?int
    {
        if (preg_match('/products\/(\d+)-/', $productUrl, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public static function formatPrice(string $price): string
    {
        return str_replace('.', '', trim($price));
    }
}
