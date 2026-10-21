<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ImageOff,
    Package,
    Pencil,
    Plus,
    Search,
    Trash2,
    X,
} from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import Pagination from '@/components/Pagination.vue';
import { cn } from '@/lib/utils';
import { formatCurrency } from '@/lib/format';
import {
    availability as availabilityRoute,
    create as createProduct,
    destroy as destroyProduct,
    edit as editProduct,
    index as productsIndex,
} from '@/routes/admin/products';
import type {
    AvailabilityOption,
    AvailabilityValue,
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

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Productos',
                href: productsIndex(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');
const category = ref(props.filters.category ?? '');
const brand = ref(props.filters.brand ?? '');
const availability = ref(props.filters.availability ?? '');
const productToDelete = ref<Product | null>(null);

let debounceTimer: ReturnType<typeof setTimeout> | undefined;

function applyFilters(): void {
    const params: Record<string, string> = {};

    if (search.value.trim()) params.search = search.value.trim();
    if (category.value) params.categoria = category.value;
    if (brand.value) params.marca = brand.value;
    if (availability.value) params.disponibilidad = availability.value;

    router.get(productsIndex().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['products', 'filters'],
    });
}

watch(search, () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 350);
});

watch([category, brand, availability], applyFilters);

onBeforeUnmount(() => {
    if (debounceTimer) clearTimeout(debounceTimer);
});

function changeAvailability(product: Product, value: AvailabilityValue): void {
    if (product.availability === value) return;

    router.patch(
        availabilityRoute(product.id).url,
        { availability: value },
        { preserveScroll: true },
    );
}

function confirmDelete(): void {
    if (!productToDelete.value) return;

    router.delete(destroyProduct(productToDelete.value.id).url, {
        preserveScroll: true,
        onFinish: () => (productToDelete.value = null),
    });
}

function clearFilters(): void {
    search.value = '';
    category.value = '';
    brand.value = '';
    availability.value = '';
}

const activeBadge: Record<AvailabilityValue, string> = {
    in_stock: 'bg-emerald-600 text-white',
    out_of_stock: 'bg-[#c63427] text-white',
    on_demand: 'bg-amber-500 text-white',
};
</script>

<template>
    <Head title="Productos" />

    <div class="flex flex-col gap-5 p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1
                    class="flex items-center gap-2 text-xl font-bold text-slate-900"
                >
                    <Package class="size-5 text-[#042163]" />
                    Inventario de productos
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ products.total }} productos registrados. Cambia la
                    disponibilidad con un clic.
                </p>
            </div>

            <Link
                :href="createProduct()"
                class="inline-flex items-center gap-2 rounded-lg bg-[#042163] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#042163]/90"
            >
                <Plus class="size-4" />
                Nuevo producto
            </Link>
        </div>

        <div
            class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 lg:flex-row lg:items-center"
        >
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400"
                />
                <input
                    v-model="search"
                    type="search"
                    placeholder="Buscar por nombre o SKU…"
                    class="h-9 w-full rounded-md border border-slate-200 pr-3 pl-9 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                />
            </div>

            <select
                v-model="category"
                class="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[#042163]"
            >
                <option value="">Todas las categorías</option>
                <option
                    v-for="item in categories"
                    :key="item.id"
                    :value="item.slug"
                >
                    {{ item.name }}
                </option>
            </select>

            <select
                v-model="brand"
                class="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[#042163]"
            >
                <option value="">Todas las marcas</option>
                <option
                    v-for="item in brands"
                    :key="item.id"
                    :value="item.slug"
                >
                    {{ item.name }}
                </option>
            </select>

            <select
                v-model="availability"
                class="h-9 rounded-md border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[#042163]"
            >
                <option value="">Toda disponibilidad</option>
                <option
                    v-for="option in availabilityOptions"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </option>
            </select>

            <button
                v-if="search || category || brand || availability"
                type="button"
                class="inline-flex h-9 items-center gap-1 rounded-md border border-slate-200 px-3 text-xs font-semibold text-slate-600 hover:text-[#c63427]"
                @click="clearFilters"
            >
                <X class="size-3.5" />
                Limpiar
            </button>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-slate-200 bg-white"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-4xl text-sm">
                    <thead
                        class="bg-slate-50 text-left text-xs text-slate-500 uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-semibold">Producto</th>
                            <th class="px-4 py-3 font-semibold">Categoría</th>
                            <th class="px-4 py-3 font-semibold">Marca</th>
                            <th class="px-4 py-3 font-semibold">Precio</th>
                            <th class="px-4 py-3 font-semibold">
                                Disponibilidad
                            </th>
                            <th class="px-4 py-3 text-right font-semibold">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="product in products.data"
                            :key="product.id"
                            class="hover:bg-slate-50/70"
                        >
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-11 shrink-0 overflow-hidden rounded-lg bg-slate-100"
                                    >
                                        <img
                                            v-if="product.image_url"
                                            :src="product.image_url"
                                            :alt="product.name"
                                            class="size-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex size-full items-center justify-center text-slate-400"
                                        >
                                            <ImageOff class="size-4" />
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="truncate font-semibold text-[#042163]"
                                        >
                                            {{ product.name }}
                                        </p>
                                        <p
                                            class="font-mono text-[11px] tracking-wide text-slate-500 uppercase"
                                        >
                                            {{ product.sku }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ product.category?.name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ product.brand?.name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-[#042163]">
                                {{ formatCurrency(product.price) }}
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="inline-flex overflow-hidden rounded-lg border border-slate-200"
                                >
                                    <button
                                        v-for="option in availabilityOptions"
                                        :key="option.value"
                                        type="button"
                                        :class="
                                            cn(
                                                'px-2.5 py-1.5 text-xs font-semibold transition',
                                                product.availability ===
                                                    option.value
                                                    ? activeBadge[option.value]
                                                    : 'bg-white text-slate-500 hover:bg-slate-50',
                                            )
                                        "
                                        @click="
                                            changeAvailability(
                                                product,
                                                option.value,
                                            )
                                        "
                                    >
                                        {{ option.label }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Link
                                        :href="editProduct(product.id)"
                                        class="flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-[#042163]/10 hover:text-[#042163]"
                                        aria-label="Editar producto"
                                    >
                                        <Pencil class="size-4" />
                                    </Link>
                                    <button
                                        type="button"
                                        class="flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-red-50 hover:text-[#c63427]"
                                        aria-label="Eliminar producto"
                                        @click="productToDelete = product"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="products.data.length === 0">
                            <td
                                colspan="6"
                                class="px-4 py-16 text-center text-sm text-slate-500"
                            >
                                No hay productos que coincidan con los filtros.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination v-if="products.last_page > 1" :links="products.links" />

        <Dialog
            :open="productToDelete !== null"
            @update:open="(open) => !open && (productToDelete = null)"
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Eliminar producto</DialogTitle>
                    <DialogDescription>
                        ¿Seguro que deseas eliminar
                        <strong>{{ productToDelete?.name }}</strong
                        >? Esta acción no se puede deshacer.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose
                        class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Cancelar
                    </DialogClose>
                    <button
                        type="button"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#c63427] px-4 text-sm font-semibold text-white hover:bg-[#a82b20]"
                        @click="confirmDelete"
                    >
                        Eliminar
                    </button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
