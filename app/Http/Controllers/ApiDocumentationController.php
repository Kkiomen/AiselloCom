<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Kontroler dokumentacji API.
 *
 * Obsługuje wyświetlanie dokumentacji dla poszczególnych endpointów API.
 * Handles displaying documentation for individual API endpoints.
 */
class ApiDocumentationController extends Controller
{
    /**
     * Wyświetla dokumentację dla konkretnego API.
     * Display documentation for specific API.
     *
     * @param string $slug Identyfikator API
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        // Sprawdź czy dokumentacja istnieje / Check if documentation exists
        $validSlugs = ['product-description'];

        if (!in_array($slug, $validSlugs)) {
            abort(404, 'Documentation not found');
        }

        return view('api-documentation.' . $slug);
    }
}
