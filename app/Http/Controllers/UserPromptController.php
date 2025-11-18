<?php

namespace App\Http\Controllers;

use App\Models\UserPrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * User Prompt Controller
 *
 * Kontroler zarządzający promptami użytkownika
 * Controller managing user prompts
 */
class UserPromptController extends Controller
{
    /**
     * Wyświetla listę promptów użytkownika
     * Display a listing of user prompts
     */
    public function index(Request $request)
    {
        $query = Auth::user()->userPrompts()->latest();

        // Filtruj po api_type jeśli podano / Filter by api_type if provided
        if ($request->has('api_type')) {
            $query->forApi($request->api_type);
        }

        $prompts = $query->get();

        // Grupuj prompty po api_type / Group prompts by api_type
        $promptsByApi = $prompts->groupBy('api_type');

        return view('user-prompts.index', compact('prompts', 'promptsByApi'));
    }

    /**
     * Wyświetla formularz tworzenia nowego promptu
     * Show the form for creating a new prompt
     */
    public function create(Request $request)
    {
        // Pobierz api_type z query string lub użyj domyślnego
        // Get api_type from query string or use default
        $apiType = $request->get('api_type', 'product-description');

        return view('user-prompts.create', compact('apiType'));
    }

    /**
     * Zapisuje nowy prompt
     * Store a newly created prompt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_type' => 'required|string|max:100',
            'prompt_template' => 'required|string|max:10000',
            'is_default' => 'boolean',
        ]);

        $prompt = Auth::user()->userPrompts()->create($validated);

        // Jeśli ustawiono jako domyślny, odznacz inne
        // If set as default, unset others
        if ($request->boolean('is_default')) {
            $prompt->setAsDefault();
        }

        // Przekieruj z powrotem do playground jeśli jest redirect_to
        // Redirect back to playground if there's redirect_to
        if ($request->has('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', __('ui.prompts.create_success'));
        }

        return redirect()->route('user-prompts.index')
            ->with('success', __('ui.prompts.create_success'));
    }

    /**
     * Wyświetla szczegóły promptu
     * Display the specified prompt
     */
    public function show(UserPrompt $userPrompt)
    {
        // Sprawdź czy prompt należy do użytkownika
        // Check if prompt belongs to user
        if ($userPrompt->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user-prompts.show', compact('userPrompt'));
    }

    /**
     * Wyświetla formularz edycji promptu
     * Show the form for editing prompt
     */
    public function edit(UserPrompt $userPrompt)
    {
        // Sprawdź czy prompt należy do użytkownika
        // Check if prompt belongs to user
        if ($userPrompt->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user-prompts.edit', compact('userPrompt'));
    }

    /**
     * Aktualizuje prompt
     * Update the specified prompt
     */
    public function update(Request $request, UserPrompt $userPrompt)
    {
        // Sprawdź czy prompt należy do użytkownika
        // Check if prompt belongs to user
        if ($userPrompt->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prompt_template' => 'required|string|max:10000',
            'is_default' => 'boolean',
        ]);

        $userPrompt->update($validated);

        // Jeśli ustawiono jako domyślny, odznacz inne
        // If set as default, unset others
        if ($request->boolean('is_default')) {
            $userPrompt->setAsDefault();
        }

        // Przekieruj z powrotem do playground jeśli jest redirect_to
        // Redirect back to playground if there's redirect_to
        if ($request->has('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', __('ui.prompts.update_success'));
        }

        return redirect()->route('user-prompts.index')
            ->with('success', __('ui.prompts.update_success'));
    }

    /**
     * Usuwa prompt
     * Remove the specified prompt
     */
    public function destroy(UserPrompt $userPrompt)
    {
        // Sprawdź czy prompt należy do użytkownika
        // Check if prompt belongs to user
        if ($userPrompt->user_id !== Auth::id()) {
            abort(403);
        }

        $userPrompt->delete();

        return redirect()->route('user-prompts.index')
            ->with('success', __('ui.prompts.delete_success'));
    }

    /**
     * Ustawia prompt jako domyślny
     * Set prompt as default
     */
    public function setDefault(UserPrompt $userPrompt)
    {
        // Sprawdź czy prompt należy do użytkownika
        // Check if prompt belongs to user
        if ($userPrompt->user_id !== Auth::id()) {
            abort(403);
        }

        $userPrompt->setAsDefault();

        return redirect()->back()
            ->with('success', __('ui.prompts.set_default_success'));
    }
}
