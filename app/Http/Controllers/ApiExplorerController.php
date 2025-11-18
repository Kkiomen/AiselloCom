<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * API Explorer Controller
 *
 * Kontroler obs\u0142uguj\u0105cy eksplorator dost\u0119pnych API
 * Controller handling available API explorer
 */
class ApiExplorerController extends Controller
{
    /**
     * Wy\u015bwietla list\u0119 dost\u0119pnych API
     * Display list of available APIs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lista dost\u0119pnych API (hardcoded na pocz\u0105tek) / List of available APIs (hardcoded for now)
        $apis = [
            [
                'id' => 'product-description',
                'slug' => 'product-description',
                'name' => 'Product Description Generator',
                'name_pl' => 'Generator Opis\u00f3w Produkt\u00f3w',
                'description' => 'Generate professional product descriptions powered by AI with web scraping enhancement',
                'description_pl' => 'Generuj profesjonalne opisy produkt\u00f3w z AI i wzbogacaniem danych z internetu',
                'category' => 'ai',
                'icon' => 'sparkles',
                'endpoint' => 'POST /api/v1/products/generate-description',
                'status' => 'active',
            ],
        ];

        return view('api-explorer.index', compact('apis'));
    }
}
