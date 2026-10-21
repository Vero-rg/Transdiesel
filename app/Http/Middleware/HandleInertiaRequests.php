<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'brand' => [
                'logo' => $this->brandAsset('logo-transdiesel'),
                'logo_white' => $this->brandAsset('logo-transdiesel-blanco'),
            ],
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Resolve the URL of a brand image stored in public/images, or null when it is missing.
     */
    private function brandAsset(string $name): ?string
    {
        foreach (['png', 'svg', 'webp', 'avif', 'jpg', 'jpeg', 'jfif'] as $extension) {
            $path = "images/{$name}.{$extension}";

            if (is_file(public_path($path))) {
                return "/{$path}";
            }
        }

        return null;
    }
}
