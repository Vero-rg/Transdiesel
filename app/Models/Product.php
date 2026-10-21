<?php

namespace App\Models;

use App\Enums\Availability;
use Carbon\CarbonInterface;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $category_id
 * @property int|null $brand_id
 * @property string $sku
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property array<string, string>|null $specifications
 * @property float $price
 * @property Availability $availability
 * @property string|null $image_path
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 * @property-read string|null $image_url
 * @property-read Category $category
 * @property-read Brand|null $brand
 * @property-read Collection<int, Review> $reviews
 * @property-read Collection<int, Review> $approvedReviews
 * @property-read int|null $approved_reviews_count
 * @property-read float|null $approved_reviews_avg_rating
 */
#[Fillable([
    'category_id',
    'brand_id',
    'sku',
    'name',
    'slug',
    'description',
    'specifications',
    'price',
    'availability',
    'image_path',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $appends = ['image_url'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'price' => 'float',
            'availability' => Availability::class,
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @param  array{search?: string|null, category?: string|null, brand?: string|null, availability?: string|null}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, function (Builder $query, string $category): void {
                $query->whereHas('category', fn (Builder $q) => $q->where('slug', $category));
            })
            ->when($filters['brand'] ?? null, function (Builder $query, string $brand): void {
                $query->whereHas('brand', fn (Builder $q) => $q->where('slug', $brand));
            })
            ->when($filters['availability'] ?? null, function (Builder $query, string $availability): void {
                $query->where('availability', $availability);
            });
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->image_path === null
                ? null
                : Storage::disk('public')->url($this->image_path),
        );
    }
}
