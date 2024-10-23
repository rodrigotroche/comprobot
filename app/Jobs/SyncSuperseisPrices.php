<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\StoreUrl;
use App\Models\Store;
use App\Models\StoreProduct;
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
            ini_set('memory_limit', '512M');

            Log::info('Iniciando sincronización de Superseis');

            $store = Store::where('name', 'Superseis')->first();

            if (!$store) {
                Log::error('No se encontró la tienda Superseis');
                return;
            }

            $urls = StoreUrl::where('store_id', $store->id)->where('enabled', true)->get();

            Log::info('Cantidad de URLs encontradas: ' . count($urls));

            foreach ($urls as $key => $storeUrl) {
                if ($key > 0) {
                    Log::info('Esperando 5 segundos antes de continuar');
                    sleep(5);
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

                    DB::beginTransaction();

                    // Buscamos o creamos el producto general usando el SKU
                    $sku = null; // Asume que obtienes el SKU en algún lugar o lo recibes
                    $product = Product::where('sku', $sku)->firstOrCreate([
                        'name' => $name,
                        'description' => $name,
                    ]);

                    // Actualizamos o creamos el store_product utilizando el external_id
                    $this->updateOrCreateStoreProduct($store, $product, $price, $productUrl, $imageName, $externalId, $sku);

                    DB::commit();
                });

                $storeUrl->updated_at = now();
                $storeUrl->save();
            }

            Log::info('Sincronización de Superseis finalizado');
        } catch (\Exception $e) {
            Log::error('Error durante la sincronización: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Método para extraer el ID del producto desde la URL.
     */
    public function extractProductIdFromUrl(string $productUrl): ?int
    {
        if (preg_match('/products\/(\d+)-/', $productUrl, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Método para guardar o actualizar un producto utilizando el ID extraído de la URL.
     */
    public function updateOrCreateProductById(int $productId, string $name): Product
    {
        return Product::updateOrCreate(
            [
                'external_id' => $productId,
            ],
            [
                'name' => $name,
                'description' => $name,
            ]
        );
    }

    public static function createHistory(Store $store, Product $product, string $formattedPrice): void
    {
        $today = now()->format('Y-m-d');
        $existingHistory = PriceHistory::where('store_id', $store->id)
            ->where('product_id', $product->id)
            ->whereDate('created_at', $today)
            ->first();

        if ($existingHistory && $existingHistory->price == $formattedPrice) {
            Log::info("El precio no ha cambiado para el producto {$product->id} en el día {$today}, no se crea un nuevo historial.");
        } else {
            $priceHistory = PriceHistory::create([
                'store_id' => $store->id,
                'product_id' => $product->id,
                'price' => $formattedPrice,
            ]);

            Log::info('Historial de precios creado: ' . json_encode($priceHistory));
        }
    }

    public function updateOrCreateStoreProduct(Store $store, Product $product, string $price, string $url, string $image, int $externalId, string $sku = null): void
    {
        StoreProduct::updateOrCreate(
            [
                'store_id' => $store->id,
                'product_id' => $product->id,
                'external_id' => $externalId,  // Usamos external_id para identificar el producto de la tienda
            ],
            [
                'sku' => $sku,  // Aunque el sku sigue siendo relevante, lo manejamos en store_products
                'price' => $price,
                'url' => $url,
                'image' => $image,
                'previous_price' => null,  // Asume que manejarás esto como en tu código original
            ]
        );
    }


    public static function formatPrice(string $price): string
    {
        return str_replace('.', '', trim($price));
    }
}
