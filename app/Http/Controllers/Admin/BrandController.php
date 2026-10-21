<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    /**
     * List the specialized brands managed by the store.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Brands/Index', [
            'brands' => Brand::query()
                ->withCount('products')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Register a new brand.
     */
    public function store(BrandRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $brand = new Brand;
        $brand->name = $validated['name'];
        $brand->slug = $this->uniqueSlug($validated['slug'] ?? $validated['name']);
        $brand->is_active = $request->boolean('is_active', true);
        $brand->logo_path = $this->storeLogo($request->file('logo'));
        $brand->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Marca registrada correctamente.']);

        return back();
    }

    /**
     * Update the brand data.
     */
    public function update(BrandRequest $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validated();

        $brand->name = $validated['name'];
        $brand->slug = $validated['slug'] ?? $brand->slug;

        if ($request->has('is_active')) {
            $brand->is_active = $request->boolean('is_active');
        }

        if ($request->hasFile('logo')) {
            $this->deleteLogo($brand->logo_path);
            $brand->logo_path = $this->storeLogo($request->file('logo'));
        }

        $brand->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Marca actualizada.']);

        return back();
    }

    /**
     * Toggle the active flag of a brand.
     */
    public function updateStatus(Request $request, Brand $brand): RedirectResponse
    {
        $request->validate([
            'is_active' => ['required'],
        ]);

        $brand->update(['is_active' => $request->boolean('is_active')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Estado de la marca actualizado.']);

        return back();
    }

    /**
     * Delete a brand.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $this->deleteLogo($brand->logo_path);
        $brand->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Marca eliminada.']);

        return back();
    }

    private function uniqueSlug(string $source, ?Brand $ignore = null): string
    {
        $base = Str::slug($source) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($slug, $ignore)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?Brand $ignore): bool
    {
        $query = Brand::query()->where('slug', $slug);

        if ($ignore instanceof Brand) {
            $query->whereKeyNot($ignore->getKey());
        }

        return $query->exists();
    }

    private function storeLogo(?UploadedFile $logo): ?string
    {
        if (! $logo instanceof UploadedFile) {
            return null;
        }

        $path = $logo->store('brands', 'public');

        return $path === false ? null : $path;
    }

    private function deleteLogo(?string $path): void
    {
        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }
    }
}
