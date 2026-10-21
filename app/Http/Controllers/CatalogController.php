<?php

namespace App\Http\Controllers;

use App\Enums\Availability;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    /**
     * Show the filterable product catalog.
     */
    public function index(Request $request): Response
    {
        $filters = [
            'search' => $this->stringFilter($request, 'search'),
            'category' => $this->stringFilter($request, 'categoria'),
            'brand' => $this->stringFilter($request, 'marca'),
            'availability' => $this->stringFilter($request, 'disponibilidad'),
        ];

        $products = Product::query()
            ->with(['category:id,name,slug', 'brand:id,name,slug'])
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating')
            ->filter($filters)
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Catalog/Index', [
            'products' => $products,
            'categories' => Category::query()
                ->withCount('products')
                ->orderBy('id')
                ->get(['id', 'name', 'slug']),
            'brands' => Brand::query()
                ->where('is_active', true)
                ->withCount('products')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'logo_path']),
            'availabilityOptions' => Availability::options(),
            'filters' => $filters,
        ]);
    }

    /**
     * Show a single product with its approved reviews and related items.
     */
    public function show(Product $product): Response
    {
        $product->load(['category:id,name,slug', 'brand:id,name,slug']);

        $reviews = $product->approvedReviews()->latest()->get();

        $relatedProducts = Product::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->withAvg('approvedReviews', 'rating')
            ->whereKeyNot($product->id)
            ->where(function (Builder $query) use ($product): void {
                $query->where('brand_id', $product->brand_id)
                    ->orWhere('category_id', $product->category_id);
            })
            ->limit(4)
            ->get();

        return Inertia::render('Catalog/Show', [
            'product' => $product,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts,
            'ratingAverage' => round((float) $reviews->avg('rating'), 1),
            'reviewsCount' => $reviews->count(),
        ]);
    }

    private function stringFilter(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
