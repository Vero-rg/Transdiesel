<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Availability;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * List every product with server-side filters.
     */
    public function index(Request $request): Response
    {
        $filters = [
            'search' => $this->stringFilter($request, 'search'),
            'category' => $this->stringFilter($request, 'categoria'),
            'brand' => $this->stringFilter($request, 'marca'),
            'availability' => $this->stringFilter($request, 'disponibilidad'),
        ];

        return Inertia::render('Admin/Products/Index', [
            'products' => Product::query()
                ->with(['category:id,name,slug', 'brand:id,name,slug'])
                ->filter($filters)
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'categories' => $this->categories(),
            'brands' => $this->brands(),
            'availabilityOptions' => Availability::options(),
            'filters' => $filters,
        ]);
    }

    /**
     * Show the product creation form.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => $this->categories(),
            'brands' => $this->brands(),
            'availabilityOptions' => Availability::options(),
        ]);
    }

    /**
     * Persist a new product.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $product = new Product;
        $product->fill($this->payload($validated));
        $product->slug = $this->uniqueSlug($validated['slug'] ?? $validated['name']);
        $product->specifications = $this->specifications($validated['specifications'] ?? []);
        $product->image_path = $this->storeImage($request->file('image'));
        $product->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Producto creado correctamente.']);

        return to_route('admin.products.index');
    }

    /**
     * Show the product edition form.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $this->categories(),
            'brands' => $this->brands(),
            'availabilityOptions' => Availability::options(),
        ]);
    }

    /**
     * Update an existing product.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $product->fill($this->payload($validated));
        $product->slug = $validated['slug'] ?? $product->slug;
        $product->specifications = $this->specifications($validated['specifications'] ?? []);

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image_path);
            $product->image_path = $this->storeImage($request->file('image'));
        }

        $product->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Producto actualizado correctamente.']);

        return to_route('admin.products.index');
    }

    /**
     * Update only the availability flag (inline toggle).
     */
    public function updateAvailability(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'availability' => ['required', Rule::enum(Availability::class)],
        ]);

        $product->update(['availability' => $validated['availability']]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Disponibilidad actualizada.']);

        return back();
    }

    /**
     * Delete a product and its image.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product->image_path);
        $product->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Producto eliminado.']);

        return back();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated): array
    {
        return [
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'] ?? null,
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'availability' => $validated['availability'],
        ];
    }

    /**
     * @param  array<int, array{key?: string|null, value?: string|null}>  $rows
     * @return array<string, string>
     */
    private function specifications(array $rows): array
    {
        $specifications = [];

        foreach ($rows as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            $value = trim((string) ($row['value'] ?? ''));

            if ($key !== '' && $value !== '') {
                $specifications[$key] = $value;
            }
        }

        return $specifications;
    }

    private function uniqueSlug(string $source, ?Product $ignore = null): string
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

    private function slugExists(string $slug, ?Product $ignore): bool
    {
        $query = Product::query()->where('slug', $slug);

        if ($ignore instanceof Product) {
            $query->whereKeyNot($ignore->getKey());
        }

        return $query->exists();
    }

    private function storeImage(?UploadedFile $image): ?string
    {
        if (! $image instanceof UploadedFile) {
            return null;
        }

        $path = $image->store('products', 'public');

        return $path === false ? null : $path;
    }

    private function deleteImage(?string $path): void
    {
        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return Collection<int, Category>
     */
    private function categories(): Collection
    {
        return Category::query()->orderBy('id')->get(['id', 'name', 'slug']);
    }

    /**
     * @return Collection<int, Brand>
     */
    private function brands(): Collection
    {
        return Brand::query()->orderBy('name')->get(['id', 'name', 'slug', 'logo_path']);
    }

    private function stringFilter(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
