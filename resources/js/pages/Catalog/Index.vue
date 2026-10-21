<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { PackageSearch, Search, SlidersHorizontal, X } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import Pagination from '@/components/Pagination.vue';
import ProductCard from '@/components/ProductCard.vue';
import { cn } from '@/lib/utils';
import { index as catalogIndex } from '@/routes/catalog';
import type {
    AvailabilityOption,
    Brand,
    CatalogFilters,
    Category,
    Paginated,
    Product,
} from '@/types';

const props = defineProps<{
    products: Paginated<Product>;
    categories: Category[];
    brands: Brand[];
    availabilityOptions: AvailabilityOption[];
    filters: CatalogFilters;
}>();

const search = ref(props.filters.search ?? '');
const showFilters = ref(false);
let debounceTimer: ReturnType<typeof setTimeout> | undefined;

const activeCategory = ref<string | null>(props.filters.category);
const activeBrand = ref<string | null>(props.filters.brand);
const activeAvailability = ref<string | null>(props.filters.availability);

type FilterOverrides = {
    categoria?: string | null;
    marca?: string | null;
    disponibilidad?: string | null;
    search?: string | null;
};

function applyFilters(overrides: FilterOverrides = {}): void {
    const params: Record<string, string> = {};

    const values = {
        search: overrides.search ?? search.value,
        categoria: overrides.categoria ?? activeCategory.value,
        marca: overrides.marca ?? activeBrand.value,
        disponibilidad: overrides.disponibilidad ?? activeAvailability.value,
    };

    Object.entries(values).forEach(([key, value]) => {
        if (value && value.trim() !== '') {
            params[key] = value;
        }
    });

    router.get(catalogIndex().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['products', 'filters'],
    });
}

function selectCategory(slug: string | null): void {
    activeCategory.value = slug;
    applyFilters({ categoria: slug });
}

function selectBrand(slug: string | null): void {
    activeBrand.value = activeBrand.value === slug ? null : slug;
    applyFilters({ marca: activeBrand.value });
}

function selectAvailability(value: string | null): void {
    activeAvailability.value =
        activeAvailability.value === value ? null : value;
    applyFilters({ disponibilidad: activeAvailability.value });
}

function clearFilters(): void {
    search.value = '';
    activeCategory.value = null;
    activeBrand.value = null;
    activeAvailability.value = null;
    router.get(
        catalogIndex().url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['products', 'filters'],
        },
    );
}

watch(search, () => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout(
        () => applyFilters({ search: search.value }),
        350,
    );
});

onBeforeUnmount(() => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
});

const hasActiveFilters = (): boolean =>
    Boolean(
        search.value ||
        activeCategory.value ||
        activeBrand.value ||
        activeAvailability.value,
    );
</script>

<template>
    <Head title="Catálogo de bombas e inyectores diésel" />

    <section class="bg-[#042163] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4">
            <p
                class="text-xs font-bold tracking-[0.16em] text-[#f8b4ad] uppercase"
            >
                Catálogo
            </p>
            <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                Bombas e inyectores diésel
            </h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-200">
                Filtra por categoría, marca o palabra clave. Todos los precios
                son en pesos mexicanos y la disponibilidad se actualiza en
                tiempo real.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    :class="
                        cn(
                            'rounded-full px-4 py-2 text-sm font-semibold transition',
                            activeCategory === null
                                ? 'bg-[#042163] text-white'
                                : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-[#042163]',
                        )
                    "
                    @click="selectCategory(null)"
                >
                    Todas
                </button>
                <button
                    v-for="category in categories"
                    :key="category.id"
                    type="button"
                    :class="
                        cn(
                            'rounded-full px-4 py-2 text-sm font-semibold transition',
                            activeCategory === category.slug
                                ? 'bg-[#042163] text-white'
                                : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-[#042163]',
                        )
                    "
                    @click="selectCategory(category.slug)"
                >
                    {{ category.name }}
                    <span class="ml-1 text-xs opacity-70">
                        ({{ category.products_count ?? 0 }})
                    </span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative flex-1 sm:w-72">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400"
                    />
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Buscar por nombre o SKU…"
                        class="h-10 w-full rounded-xl border border-slate-200 bg-white pr-3 pl-9 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                    />
                </div>

                <button
                    type="button"
                    class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 lg:hidden"
                    @click="showFilters = !showFilters"
                >
                    <SlidersHorizontal class="size-4" />
                    Filtros
                </button>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[260px_1fr]">
            <aside
                :class="[
                    'space-y-6 rounded-2xl border border-slate-200 bg-white p-5 lg:block',
                    showFilters ? 'block' : 'hidden',
                ]"
            >
                <div>
                    <div class="flex items-center justify-between">
                        <h2
                            class="text-xs font-bold tracking-[0.12em] text-[#042163] uppercase"
                        >
                            Marcas
                        </h2>
                        <button
                            v-if="activeBrand"
                            type="button"
                            class="text-[11px] font-semibold text-[#c63427] hover:underline"
                            @click="selectBrand(activeBrand)"
                        >
                            Limpiar
                        </button>
                    </div>
                    <ul class="mt-3 space-y-1">
                        <li v-for="brand in brands" :key="brand.id">
                            <button
                                type="button"
                                :class="
                                    cn(
                                        'flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition',
                                        activeBrand === brand.slug
                                            ? 'bg-[#042163] text-white'
                                            : 'text-slate-600 hover:bg-slate-100 hover:text-[#042163]',
                                    )
                                "
                                @click="selectBrand(brand.slug)"
                            >
                                <span class="font-medium">{{
                                    brand.name
                                }}</span>
                                <span class="text-xs opacity-70">
                                    {{ brand.products_count ?? 0 }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="border-t border-slate-200 pt-5">
                    <h2
                        class="text-xs font-bold tracking-[0.12em] text-[#042163] uppercase"
                    >
                        Disponibilidad
                    </h2>
                    <ul class="mt-3 space-y-1">
                        <li
                            v-for="option in availabilityOptions"
                            :key="option.value"
                        >
                            <button
                                type="button"
                                :class="
                                    cn(
                                        'flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition',
                                        activeAvailability === option.value
                                            ? 'bg-[#c63427] text-white'
                                            : 'text-slate-600 hover:bg-slate-100 hover:text-[#c63427]',
                                    )
                                "
                                @click="selectAvailability(option.value)"
                            >
                                {{ option.label }}
                            </button>
                        </li>
                    </ul>
                </div>

                <button
                    v-if="hasActiveFilters()"
                    type="button"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#c63427]/40 hover:text-[#c63427]"
                    @click="clearFilters"
                >
                    <X class="size-3.5" />
                    Limpiar filtros
                </button>
            </aside>

            <div>
                <p class="mb-4 text-sm text-slate-600">
                    Mostrando
                    <span class="font-semibold text-[#042163]">
                        {{ products.from ?? 0 }}–{{ products.to ?? 0 }}
                    </span>
                    de
                    <span class="font-semibold text-[#042163]">
                        {{ products.total }}
                    </span>
                    productos
                </p>

                <div
                    v-if="products.data.length > 0"
                    class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <ProductCard
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-300 bg-white py-20 text-center"
                >
                    <PackageSearch class="size-10 text-slate-400" />
                    <p class="font-semibold text-slate-700">
                        No encontramos productos con esos filtros
                    </p>
                    <p class="max-w-sm text-sm text-slate-500">
                        Prueba con otra marca o palabra clave, o contáctanos por
                        WhatsApp y lo buscamos por ti.
                    </p>
                    <button
                        type="button"
                        class="mt-2 rounded-lg bg-[#042163] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#042163]/90"
                        @click="clearFilters"
                    >
                        Limpiar filtros
                    </button>
                </div>

                <div v-if="products.last_page > 1" class="mt-10">
                    <Pagination :links="products.links" />
                </div>
            </div>
        </div>
    </div>
</template>
