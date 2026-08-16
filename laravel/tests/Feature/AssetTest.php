<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use App\Models\Category;
use App\Models\Parameter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
class AssetTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // use RefreshDatabase;

    public function test_authenticated_user_can_create_an_asset(): void
    {
        dump(config('database.default'));
        dump(config('database.connections.mysql.database'));
        dump(DB::connection()->getDatabaseName());
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/assets', [
            'name' => 'Test Laptop',
            'category_id' => 1,
            'status' => 'Available',
            'brand' => 'Dell',
            'warranty' => '2027-12-31',
            'price' => 1500,
            'selected_user' => 0,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('assets', [
            'name' => 'Test Laptop',
            'brand' => 'Dell',
            'category_id' => 1,
            'status' => 'Available',
            'price' => 1500,
        ]);
    }

    public function test_asset_creation_requires_required_fields(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/assets', []);
        if ($response->status() === 500) {
            dd($response->json(), $response->getContent());
        }

        $response->assertStatus(422);


        $response->assertJsonValidationErrors([
            'name',
            'category_id',
            'status',
            'brand',
            'warranty',
            'price',
            'selected_user',
        ]);
    }

    public function test_non_admin_user_cannot_delete_an_asset(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $asset = Asset::factory()->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson("/api/assets/{$asset->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
        ]);
    }

    public function test_asset_can_store_dynamic_attributes_for_a_category(): void {

        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'user',
        ]);
        
        $category = Category::factory()->create();


        $textParameter = Parameter::factory()->create([
            'category_id' => $category->id,
            'name' => 'Operating System',
            'data_type' => 'string',
            'is_required' => false,
        ]);

        $numberParameter = Parameter::factory()->create([
            'category_id' => $category->id,
            'name' => 'RAM',
            'data_type' => 'number',
            'is_required' => false,
        ]);
        
        
        $response = $this->actingAs($user)->postJson('/api/assets', [
            'name' => 'Test Laptop',
            'category_id' => $category->id,
            'status' => 'Available',
            'brand' => 'Dell',
            'warranty' => '2027-12-31',
            'price' => 1500,
            'selected_user' => 0,

            'attributes' => [
                $textParameter->id => 'Windows 11',
                $numberParameter->id => '16',
            ],
        ]);
        $response->dump();
        $response->assertStatus(201);
        $assetId = $response->json('id');

        $this->assertNotNull($assetId);

        $this->assertDatabaseHas('attribute_values', [
            'asset_id' => $assetId,
            'parameter_id' => $textParameter->id,
            'value' => 'Windows 11',
        ]);

        $this->assertDatabaseHas('attribute_values', [
            'asset_id' => $assetId,
            'parameter_id' => $numberParameter->id,
            'value' => '16',
        ]);
        // $asset = Asset::where('name', 'Test Laptop')->first();

        // $this->assertDatabaseHas('attribute_values', [
        //     'asset_id' => $asset->id,
        //     'parameter_id' => $textParameter->id,
        //     'value' => 'Windows 11',
        // ]);

        // $this->assertDatabaseHas('attribute_values', [
        //     'asset_id' => $asset->id,
        //     'parameter_id' => $numberParameter->id,
        //     'value' => '16',
        // ]);
    }
}
