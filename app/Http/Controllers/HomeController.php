<?php

namespace App\Http\Controllers;

use App\Enums\Availability;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Show the public landing page.
     */
    public function index(): Response
    {
        return Inertia::render('Home', [
            'categories' => Category::query()
                ->withCount('products')
                ->orderBy('id')
                ->get(['id', 'name', 'slug']),
            'brands' => Brand::query()
                ->where('is_active', true)
                ->withCount('products')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'logo_path']),
            'featuredProducts' => Product::query()
                ->with(['category:id,name,slug', 'brand:id,name,slug'])
                ->withCount('approvedReviews')
                ->withAvg('approvedReviews', 'rating')
                ->where('availability', Availability::InStock)
                ->latest()
                ->limit(6)
                ->get(),
            'stats' => [
                'products' => Product::query()->count(),
                'brands' => Brand::query()->where('is_active', true)->count(),
            ],
        ]);
    }
}
