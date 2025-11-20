<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * API Explorer Controller
 *
 * Kontroler obsługujący eksplorator dostępnych API
 * Controller handling available API explorer
 */
class ApiExplorerController extends Controller
{
    /**
     * Wyświetla listę dostępnych API
     * Display list of available APIs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lista dostępnych API (hardcoded na początek) / List of available APIs (hardcoded for now)
        $apis = [
            [
                'id' => 'product-description',
                'slug' => 'product-description',
                'name' => 'Product Description Generator',
                'name_pl' => 'Generator Opisów Produktów',
                'description' => 'Generate professional product descriptions powered by AI with web scraping enhancement',
                'description_pl' => 'Generuj profesjonalne opisy produktów z AI i wzbogacaniem danych z internetu',
                'category' => 'ai',
                'icon' => 'sparkles',
                'endpoint' => 'POST /api/v1/products/generate-description',
                'status' => 'active',
            ],
        ];

        return view('api-explorer.index', compact('apis'));
    }
}
