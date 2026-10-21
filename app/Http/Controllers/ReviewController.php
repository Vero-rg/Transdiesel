<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ReviewController extends Controller
{
    /**
     * Store a new review for the given product. Reviews await approval.
     */
    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        $product->reviews()->create([
            ...$request->validated(),
            'is_approved' => false,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '¡Gracias! Tu reseña será publicada tras ser revisada.',
        ]);

        return back();
    }
}
