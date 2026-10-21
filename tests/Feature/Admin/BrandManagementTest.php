<?php

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('non administrators cannot manage brands', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.brands.index'))
        ->assertForbidden();
});

test('administrators can list brands with their product counts', function () {
    $brand = Brand::factory()->create(['name' => 'Cummins']);
    Product::factory()->create(['brand_id' => $brand->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.brands.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Brands/Index')
            ->has('brands', 1)
            ->where('brands.0.name', 'Cummins')
            ->where('brands.0.products_count', 1));
});

test('administrators can register a brand with a logo', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->post(route('admin.brands.store'), [
            'name' => 'Caterpillar',
            'is_active' => true,
            'logo' => UploadedFile::fake()->image('caterpillar.png'),
        ])
        ->assertRedirect();

    $brand = Brand::query()->sole();

    expect($brand->slug)->toBe('caterpillar')
        ->and($brand->is_active)->toBeTrue()
        ->and($brand->logo_path)->not->toBeNull();

    Storage::disk('public')->assertExists($brand->logo_path);
});

test('administrators can register a brand with an avif logo', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->post(route('admin.brands.store'), [
            'name' => 'Avif Corp',
            'is_active' => true,
            'logo' => UploadedFile::fake()->create('avif-corp.avif', 256),
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $brand = Brand::query()->sole();

    expect($brand->logo_path)->toEndWith('.avif');

    Storage::disk('public')->assertExists($brand->logo_path);
});

test('brand names must be unique', function () {
    Brand::factory()->create(['name' => 'Denso']);

    $this->actingAs($this->admin)
        ->post(route('admin.brands.store'), ['name' => 'Denso'])
        ->assertSessionHasErrors('name');
});

test('administrators can toggle the brand status and delete it', function () {
    $brand = Brand::factory()->create(['is_active' => true]);

    $this->actingAs($this->admin)
        ->patch(route('admin.brands.status', $brand), ['is_active' => false])
        ->assertRedirect();

    expect($brand->fresh()->is_active)->toBeFalse();

    $this->actingAs($this->admin)
        ->delete(route('admin.brands.destroy', $brand))
        ->assertRedirect();

    expect(Brand::query()->count())->toBe(0);
});
