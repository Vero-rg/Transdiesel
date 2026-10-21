<?php

use App\Enums\Availability;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Inertia\Testing\AssertableInertia as Assert;

test('the home page exposes active brands, categories and in stock products', function () {
    $bombas = Category::factory()->create(['name' => 'Bombas de diésel', 'slug' => 'bombas']);
    $activeBrand = Brand::factory()->create(['is_active' => true]);
    Brand::factory()->inactive()->create();

    $featured = Product::factory()->create([
        'category_id' => $bombas->id,
        'brand_id' => $activeBrand->id,
        'availability' => Availability::InStock,
    ]);

    Product::factory()->outOfStock()->create([
        'category_id' => $bombas->id,
        'brand_id' => $activeBrand->id,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->has('categories', 1)
            ->has('brands', 1)
            ->where('brands.0.id', $activeBrand->id)
            ->has('featuredProducts', 1)
            ->where('featuredProducts.0.id', $featured->id)
            ->where('stats.products', 2));
});

test('the catalog filters products by category and brand', function () {
    $bombas = Category::factory()->create(['slug' => 'bombas']);
    $inyectores = Category::factory()->create(['slug' => 'inyectores']);
    $bosch = Brand::factory()->create(['slug' => 'bosch']);
    $cummins = Brand::factory()->create(['slug' => 'cummins']);

    $match = Product::factory()->create([
        'category_id' => $bombas->id,
        'brand_id' => $bosch->id,
    ]);

    Product::factory()->create([
        'category_id' => $inyectores->id,
        'brand_id' => $cummins->id,
    ]);

    $this->get(route('catalog.index', ['categoria' => 'bombas', 'marca' => 'bosch']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Catalog/Index')
            ->has('products.data', 1)
            ->where('products.data.0.id', $match->id)
            ->where('filters.category', 'bombas')
            ->where('filters.brand', 'bosch'));
});

test('the catalog search matches product names and skus', function () {
    $product = Product::factory()->create(['sku' => 'TD-BOM-9999']);
    Product::factory()->create();

    $this->get(route('catalog.index', ['search' => 'BOM-9999']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('products.data', 1)
            ->where('products.data.0.id', $product->id));
});

test('the product page only exposes approved reviews', function () {
    $product = Product::factory()->create();
    $approved = Review::factory()->create(['product_id' => $product->id, 'rating' => 5]);
    Review::factory()->pending()->create(['product_id' => $product->id, 'rating' => 1]);

    $this->get(route('catalog.show', $product))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Catalog/Show')
            ->where('product.id', $product->id)
            ->has('reviews', 1)
            ->where('reviews.0.id', $approved->id)
            ->where('reviewsCount', 1)
            ->where('ratingAverage', 5));
});

test('visitors can submit a review that awaits approval', function () {
    $product = Product::factory()->create();

    $this->post(route('reviews.store', $product), [
        'author_name' => 'Ana Torres',
        'author_email' => 'ana@example.com',
        'rating' => 4,
        'comment' => 'Muy buen servicio.',
    ])->assertRedirect();

    $review = Review::query()->sole();

    expect($review->product_id)->toBe($product->id)
        ->and($review->rating)->toBe(4)
        ->and($review->is_approved)->toBeFalse();
});

test('a review requires a valid rating and contact details', function () {
    $product = Product::factory()->create();

    $this->post(route('reviews.store', $product), [
        'author_name' => '',
        'author_email' => 'not-an-email',
        'rating' => 9,
    ])->assertSessionHasErrors(['author_name', 'author_email', 'rating']);

    expect(Review::query()->count())->toBe(0);
});
