<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Kontroler rejestracji użytkowników.
 *
 * Obsługuje proces rejestracji nowych użytkowników w systemie Aisello.
 * Handles the registration process for new users in the Aisello system.
 */
class RegisteredUserController extends Controller
{
    /**
     * Wyświetla widok formularza rejestracji.
     * Display the registration view.
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Obsługuje przychodzące żądanie rejestracji.
     * Handle an incoming registration request.
     *
     * Waliduje dane wejściowe, tworzy nowego użytkownika,
     * wywołuje zdarzenie rejestracji i loguje użytkownika.
     *
     * @param Request $request Żądanie HTTP z danymi rejestracji
     * @return RedirectResponse Przekierowanie do dashboardu
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Walidacja danych rejestracji / Validate registration data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Tworzenie nowego użytkownika / Create new user
        $user = User::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Wywołanie zdarzenia rejestracji / Fire registered event
        event(new Registered($user));

        // Automatyczne logowanie użytkownika / Auto-login user
        Auth::login($user);

        // Przekierowanie do pulpitu / Redirect to dashboard
        return redirect(route('dashboard', absolute: false));
    }
}
