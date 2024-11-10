<?php

namespace App\Jobs;

use App\Models\PriceHistory;
use App\Models\StoreProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\TempProduct;
use App\Models\Product;
use App\Models\StoresProduct;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SyncSuperseisProductDetail implements ShouldQueue
{
    use Queueable;

    protected $tempProduct;

    /**
     * Create a new job instance.
     */
    public function __construct(TempProduct $tempProduct)
    {
        $this->tempProduct = $tempProduct;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', $this->tempProduct->url);

            if ($response->getStatusCode() !== 200) {
                Log::error("Error al obtener la página de detalle para el producto ID {$this->tempProduct->id}");
                $this->tempProduct->update(['status' => 'error']);
                return;
            }

            $html = $response->getContent();
            $crawler = new Crawler($html);

            // Extraer el SKU del detalle de la página
            $skuNode = $crawler->filter('.sku')->first();
            if ($skuNode->count() > 0) {
                $skuText = $skuNode->text();
                $sku = trim(str_replace('Código de Barras:', '', $skuText));

                // Extraer el precio actual
                $currentPriceNode = $crawler->filter('.productPrice')->first();
                $currentPrice = null;
                if ($currentPriceNode->count() > 0) {
                    $currentPrice = $this->formatPrice(trim(str_replace(['Gs', ' '], '', $currentPriceNode->text())));
                }

                // Extraer el precio anterior (precio con descuento)
                $oldPriceNode = $crawler->filter('.oldproductPrice')->first();
                $oldPrice = null;
                if ($oldPriceNode->count() > 0) {
                    $oldPrice = $this->formatPrice(trim(str_replace(['Gs', ' '], '', $oldPriceNode->text())));
                }


                // Actualizar el producto con el SKU
                $this->tempProduct->update([
                    'status' => 'processed',
                    'last_scraped_at' => now(),
                ]);

                $product = Product::updateOrCreate([
                    'sku' => $sku,
                ], [
                    'name' => $this->tempProduct->name,
                    'description' => $this->tempProduct->description,
                ]);

                $storeProduct = StoreProduct::updateOrCreate([
                    'store_id' => $this->tempProduct->store_id,
                    'product_id' => $product->id,
                    'reference_id' => $this->tempProduct->reference_id,
                ], [
                    'sku' => $sku,
                    'url' => $this->tempProduct->url,
                    'image' => $this->tempProduct->image,
                    'previous_price' => $oldPrice,
                    'price' => $currentPrice,
                ]);

                $history = PriceHistory::create([
                    'store_id' => $this->tempProduct->store_id,
                    'product_id' => $product->id,
                    'previous_price' => $oldPrice,
                    'price' => $currentPrice,
                ]);

                $this->tempProduct->update([
                    'status' => 'processed',
                    'last_scraped_at' => now(),
                ]);

                Log::info("Producto ID {$this->tempProduct->id} actualizado con SKU: {$sku}");
            } else {
                Log::warning("No se encontró el SKU para el producto ID {$this->tempProduct->id}");
                $this->tempProduct->update(['status' => 'error']);
            }
        } catch (\Exception $e) {
            Log::error("Error procesando el detalle del producto ID {$this->tempProduct->id}: " . $e->getMessage());
            $this->tempProduct->update(['status' => 'error']);
            throw $e;
        }
    }

    public static function formatPrice(string $price): string
    {
        return str_replace('.', '', trim($price));
    }
}
