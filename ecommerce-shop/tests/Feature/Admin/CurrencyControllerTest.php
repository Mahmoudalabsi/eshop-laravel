<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 1,
        ]);
    }

    /** @test */
    public function it_can_list_currencies(): void
    {
        Currency::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.currencies.json'));

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_store_a_new_currency(): void
    {
        $data = [
            'name' => 'United Arab Emirates Dirham',
            'code' => 'AED',
            'symbol' => 'د.إ',
            'exchange_rate' => 3.67,
            'is_default' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.currencies.store'), $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Currency added successfully']);

        $this->assertDatabaseHas('currencies', [
            'code' => 'AED',
            'name' => 'United Arab Emirates Dirham'
        ]);
    }

    /** @test */
    public function it_ensures_only_one_default_currency_exists_on_store(): void
    {
        $existingDefault = Currency::factory()->default()->create(['code' => 'SAR']);

        $data = [
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 3.75, // Exchange rate will be forced to 1.0 by controller
            'is_default' => true,
        ];

        $this->actingAs($this->admin)->postJson(route('admin.currencies.store'), $data);

        $this->assertDatabaseHas('currencies', [
            'code' => 'USD',
            'is_default' => true,
            'exchange_rate' => 1.0
        ]);

        $this->assertDatabaseHas('currencies', [
            'code' => 'SAR',
            'is_default' => false
        ]);
    }

    /** @test */
    public function it_can_update_a_currency(): void
    {
        $currency = Currency::factory()->create(['name' => 'Old Name']);

        $data = [
            'name' => 'New Name',
            'code' => $currency->code,
            'symbol' => $currency->symbol,
            'exchange_rate' => 4.0,
            'is_default' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.currencies.update', $currency->id), $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Currency updated successfully']);

        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'name' => 'New Name',
            'exchange_rate' => 4.0
        ]);
    }

    /** @test */
    public function it_cannot_delete_default_currency(): void
    {
        $currency = Currency::factory()->default()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.currencies.destroy', $currency->id));

        $response->assertStatus(422)
            ->assertJson(['message' => 'Cannot delete default currency']);

        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);
    }

    /** @test */
    public function it_can_delete_non_default_currency(): void
    {
        $currency = Currency::factory()->create(['is_default' => false]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.currencies.destroy', $currency->id));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Currency deleted successfully']);

        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }

    /** @test */
    public function it_can_toggle_currency_status(): void
    {
        $currency = Currency::factory()->create(['status' => true]);

        $response = $this->actingAs($this->admin)
            ->patchJson(route('admin.currencies.status', $currency->id));

        $response->assertStatus(200);
        $this->assertEquals(false, $currency->fresh()->status);
    }
}
