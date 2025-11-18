<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPrompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testy UserPromptController
 * UserPromptController Tests
 *
 * Testuje zarządzanie promptami użytkownika z obsługą api_type
 * Tests user prompt management with api_type support
 */
class UserPromptTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Użytkownik może zobaczyć listę promptów
     * Test: User can see list of prompts
     */
    public function test_user_can_see_prompts_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user-prompts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('user-prompts.index');
    }

    /**
     * Test: Użytkownik może zobaczyć formularz tworzenia promptu
     * Test: User can see create prompt form
     */
    public function test_user_can_see_create_prompt_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user-prompts.create'));

        $response->assertStatus(200);
        $response->assertViewIs('user-prompts.create');
        $response->assertViewHas('apiType', 'product-description');
    }

    /**
     * Test: Użytkownik może zobaczyć formularz tworzenia promptu z określonym api_type
     * Test: User can see create prompt form with specific api_type
     */
    public function test_user_can_see_create_prompt_form_with_api_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user-prompts.create', ['api_type' => 'test-api']));

        $response->assertStatus(200);
        $response->assertViewHas('apiType', 'test-api');
    }

    /**
     * Test: Użytkownik może utworzyć nowy prompt
     * Test: User can create a new prompt
     */
    public function test_user_can_create_prompt(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user-prompts.store'), [
            'name' => 'Test Prompt',
            'api_type' => 'product-description',
            'prompt_template' => 'Generate description for {name}',
            'is_default' => false,
        ]);

        $response->assertRedirect(route('user-prompts.index'));
        $this->assertDatabaseHas('user_prompts', [
            'user_id' => $user->id,
            'name' => 'Test Prompt',
            'api_type' => 'product-description',
        ]);
    }

    /**
     * Test: Użytkownik może utworzyć prompt z redirect_to
     * Test: User can create prompt with redirect_to
     */
    public function test_user_can_create_prompt_with_redirect(): void
    {
        $user = User::factory()->create();
        $redirectUrl = 'http://localhost/playground/product-description';

        $response = $this->actingAs($user)->post(route('user-prompts.store'), [
            'name' => 'Test Prompt',
            'api_type' => 'product-description',
            'prompt_template' => 'Generate description for {name}',
            'is_default' => false,
            'redirect_to' => $redirectUrl,
        ]);

        $response->assertRedirect($redirectUrl);
    }

    /**
     * Test: Użytkownik może edytować swój prompt
     * Test: User can edit their prompt
     */
    public function test_user_can_edit_prompt(): void
    {
        $user = User::factory()->create();
        $prompt = UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'Original Name',
            'api_type' => 'product-description',
            'prompt_template' => 'Original template',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->get(route('user-prompts.edit', $prompt));

        $response->assertStatus(200);
        $response->assertViewIs('user-prompts.edit');
    }

    /**
     * Test: Użytkownik może zaktualizować swój prompt
     * Test: User can update their prompt
     */
    public function test_user_can_update_prompt(): void
    {
        $user = User::factory()->create();
        $prompt = UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'Original Name',
            'api_type' => 'product-description',
            'prompt_template' => 'Original template',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->put(route('user-prompts.update', $prompt), [
            'name' => 'Updated Name',
            'prompt_template' => 'Updated template',
            'is_default' => false,
        ]);

        $response->assertRedirect(route('user-prompts.index'));
        $this->assertDatabaseHas('user_prompts', [
            'id' => $prompt->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test: Użytkownik nie może edytować promptu innego użytkownika
     * Test: User cannot edit another user's prompt
     */
    public function test_user_cannot_edit_another_users_prompt(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $prompt = UserPrompt::create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Prompt',
            'api_type' => 'product-description',
            'prompt_template' => 'Template',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->get(route('user-prompts.edit', $prompt));

        $response->assertStatus(403);
    }

    /**
     * Test: Użytkownik może usunąć swój prompt
     * Test: User can delete their prompt
     */
    public function test_user_can_delete_prompt(): void
    {
        $user = User::factory()->create();
        $prompt = UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'To Delete',
            'api_type' => 'product-description',
            'prompt_template' => 'Template',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->delete(route('user-prompts.destroy', $prompt));

        $response->assertRedirect(route('user-prompts.index'));
        $this->assertDatabaseMissing('user_prompts', ['id' => $prompt->id]);
    }

    /**
     * Test: Użytkownik może ustawić prompt jako domyślny dla danego API
     * Test: User can set prompt as default for specific API
     */
    public function test_user_can_set_prompt_as_default(): void
    {
        $user = User::factory()->create();

        // Utwórz dwa prompty dla tego samego API
        // Create two prompts for the same API
        $prompt1 = UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'Prompt 1',
            'api_type' => 'product-description',
            'prompt_template' => 'Template 1',
            'is_default' => true,
        ]);

        $prompt2 = UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'Prompt 2',
            'api_type' => 'product-description',
            'prompt_template' => 'Template 2',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->post(route('user-prompts.set-default', $prompt2));

        $response->assertRedirect();

        // Sprawdź, że prompt2 jest teraz domyślny
        // Check that prompt2 is now default
        $this->assertDatabaseHas('user_prompts', [
            'id' => $prompt2->id,
            'is_default' => true,
        ]);

        // Sprawdź, że prompt1 już nie jest domyślny
        // Check that prompt1 is no longer default
        $this->assertDatabaseHas('user_prompts', [
            'id' => $prompt1->id,
            'is_default' => false,
        ]);
    }

    /**
     * Test: Domyślny prompt jest per API type
     * Test: Default prompt is per API type
     */
    public function test_default_prompt_is_per_api_type(): void
    {
        $user = User::factory()->create();

        // Utwórz prompty dla różnych API
        // Create prompts for different APIs
        $promptApi1 = UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'API 1 Prompt',
            'api_type' => 'product-description',
            'prompt_template' => 'Template 1',
            'is_default' => true,
        ]);

        $promptApi2 = UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'API 2 Prompt',
            'api_type' => 'another-api',
            'prompt_template' => 'Template 2',
            'is_default' => true,
        ]);

        // Oba mogą być domyślne dla różnych API
        // Both can be default for different APIs
        $this->assertTrue($promptApi1->fresh()->is_default);
        $this->assertTrue($promptApi2->fresh()->is_default);
    }

    /**
     * Test: Walidacja - nazwa jest wymagana
     * Test: Validation - name is required
     */
    public function test_validation_name_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user-prompts.store'), [
            'api_type' => 'product-description',
            'prompt_template' => 'Template',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test: Walidacja - api_type jest wymagane
     * Test: Validation - api_type is required
     */
    public function test_validation_api_type_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user-prompts.store'), [
            'name' => 'Test',
            'prompt_template' => 'Template',
        ]);

        $response->assertSessionHasErrors('api_type');
    }

    /**
     * Test: Walidacja - prompt_template jest wymagane
     * Test: Validation - prompt_template is required
     */
    public function test_validation_prompt_template_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user-prompts.store'), [
            'name' => 'Test',
            'api_type' => 'product-description',
        ]);

        $response->assertSessionHasErrors('prompt_template');
    }

    /**
     * Test: Niezalogowany użytkownik jest przekierowywany
     * Test: Unauthenticated user is redirected
     */
    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get(route('user-prompts.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Prompty są filtrowane po api_type
     * Test: Prompts are filtered by api_type
     */
    public function test_prompts_filtered_by_api_type(): void
    {
        $user = User::factory()->create();

        // Utwórz prompty dla różnych API
        // Create prompts for different APIs
        UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'Product Prompt',
            'api_type' => 'product-description',
            'prompt_template' => 'Template 1',
            'is_default' => false,
        ]);

        UserPrompt::create([
            'user_id' => $user->id,
            'name' => 'Another Prompt',
            'api_type' => 'another-api',
            'prompt_template' => 'Template 2',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->get(route('user-prompts.index', ['api_type' => 'product-description']));

        $response->assertStatus(200);
        $response->assertSee('Product Prompt');
        $response->assertDontSee('Another Prompt');
    }
}
