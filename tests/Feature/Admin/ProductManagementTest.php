<?php

use App\Enums\Availability;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->category = Category::factory()->create(['name' => 'Bombas de diésel']);
    $this->brand = Brand::factory()->create(['name' => 'Bosch']);
});

test('guests are redirected to login from the admin panel', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('login'));
});

test('non administrators cannot access the admin panel', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.products.index'))
        ->assertForbidden();
});

test('administrators can see the paginated inventory', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Products/Index')
            ->has('products.data', 1)
            ->where('products.data.0.id', $product->id)
            ->has('categories', 1)
            ->has('brands', 1)
            ->has('availabilityOptions', 3));
});

test('administrators can create a product with a technical sheet', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), [
            'name' => 'Bomba de inyección Bosch VP44',
            'sku' => 'TD-BOM-5000',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => '18900.50',
            'availability' => Availability::InStock->value,
            'description' => 'Bomba reconstruida y calibrada.',
            'specifications' => [
                ['key' => 'Presión de trabajo', 'value' => '1,600 bar'],
                ['key' => '', 'value' => 'descartado'],
            ],
        ])
        ->assertRedirect(route('admin.products.index'));

    $product = Product::query()->sole();

    expect($product->sku)->toBe('TD-BOM-5000')
        ->and($product->slug)->toBe('bomba-de-inyeccion-bosch-vp44')
        ->and($product->availability)->toBe(Availability::InStock)
        ->and($product->specifications)->toBe(['Presión de trabajo' => '1,600 bar']);
});

test('administrators can create a product with an avif image', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), [
            'name' => 'Bomba con imagen AVIF',
            'sku' => 'TD-AVIF-1',
            'category_id' => $this->category->id,
            'price' => '1500',
            'availability' => Availability::InStock->value,
            'image' => UploadedFile::fake()->create('bomba.avif', 512),
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product = Product::query()->sole();

    expect($product->image_path)->toEndWith('.avif');

    Storage::disk('public')->assertExists($product->image_path);
});

test('the same sku cannot be duplicated', function () {
    Product::factory()->create(['sku' => 'TD-DUP-1']);

    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), [
            'name' => 'Producto duplicado',
            'sku' => 'TD-DUP-1',
            'category_id' => $this->category->id,
            'price' => '100',
            'availability' => Availability::InStock->value,
        ])
        ->assertSessionHasErrors('sku');
});

test('administrators can update the availability inline', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'availability' => Availability::InStock,
    ]);

    $this->actingAs($this->admin)
        ->patch(route('admin.products.availability', $product), [
            'availability' => Availability::OutOfStock->value,
        ])
        ->assertRedirect();

    expect($product->fresh()->availability)->toBe(Availability::OutOfStock);
});

test('administrators can update and delete a product', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Nombre anterior',
    ]);

    $this->actingAs($this->admin)
        ->patch(route('admin.products.update', $product), [
            'name' => 'Nombre nuevo',
            'sku' => $product->sku,
            'category_id' => $this->category->id,
            'price' => '2500.00',
            'availability' => Availability::OnDemand->value,
        ])
        ->assertRedirect(route('admin.products.index'));

    expect($product->fresh()->name)->toBe('Nombre nuevo')
        ->and($product->fresh()->availability)->toBe(Availability::OnDemand);

    $this->actingAs($this->admin)
        ->delete(route('admin.products.destroy', $product))
        ->assertRedirect();

    expect(Product::query()->count())->toBe(0);
});
