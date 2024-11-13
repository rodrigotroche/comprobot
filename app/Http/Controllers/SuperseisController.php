<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\StoreCategory;
use Illuminate\Http\Request;
use App\Services\SuperseisService;

class SuperseisController extends Controller
{
    private $superseisService;

    public function __construct(SuperseisService $superseisService)
    {
        $this->superseisService = $superseisService;
    }

    public function getCategories()
    {
        $categories = $this->superseisService->getCategories();

        foreach ($categories as $category) {
            $this->saveCategory($category);
        }

        return response()->json(['message' => 'Categorías guardadas correctamente']);
    }

    private function saveCategory($category, $parent = null)
    {
        $categoryModel = Category::updateOrCreate(
            ['name' => $category['name']],
            ['parent_id' => $parent ? $parent->id : null]
        );

        StoreCategory::updateOrCreate(
            ['store_id' => 1, 'category_id' => $categoryModel->id],
            ['name' => $category['name'], 'url' => $category['url']]
        );

        if (isset($category['children'])) {
            foreach ($category['children'] as $child) {
                $this->saveCategory($child, $categoryModel);
            }
        }
    }
}
