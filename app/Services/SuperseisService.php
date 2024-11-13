<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class SuperseisService
{
    public function getCategories()
    {
        $url = 'https://www.superseis.com.py/';
        $client = HttpClient::create();
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            Log::error("Error al obtener la página principal {$url}");
            return [];
        }

        $html = $response->getContent();
        $crawler = new Crawler($html);

        // Array para almacenar categorías
        $categories = [];

        // Recorrer cada categoría
        $crawler->filter('.wsshoptabing .catnav .level1 > a')->each(function (Crawler $node) use (&$categories) {
            $categoryName = $node->text();
            $categoryUrl = $node->attr('href');
            $subCategoriesLevel2 = [];

            // Obtener subcategorías de segundo nivel
            $node->nextAll()->filter('.wsmenu-submenu-sub > li > a')->each(function (Crawler $subNode) use (&$subCategoriesLevel2) {
                $subCategoryLevel2 = [
                    'name' => $subNode->text(),
                    'url' => null,
                    'children' => []
                ];

                // Obtener subcategorías de tercer nivel
                $subNode->nextAll()->filter('.wstliststy02 > li > a')->each(function (Crawler $subSubNode) use (&$subCategoryLevel2) {
                    if (! empty($subSubNode->text())) {
                        $subCategoryLevel2['children'][] = [
                            'name' => $subSubNode->text(),
                            'url' => $subSubNode->attr('href'),
                            'children' => []
                        ];
                    }
                });

                $subCategoriesLevel2[] = $subCategoryLevel2;
            });

            $categories[] = [
                'name' => $categoryName,
                'url' => $categoryUrl,
                'children' => $subCategoriesLevel2
            ];
        });

        return $categories;
    }
}
