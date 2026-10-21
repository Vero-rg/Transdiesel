<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, PackageOpen } from '@lucide/vue';
import ProductForm from '@/components/admin/ProductForm.vue';
import { index as productsIndex } from '@/routes/admin/products';
import { show as catalogShow } from '@/routes/catalog';
import type { AvailabilityOption, Brand, Category, Product } from '@/types';

defineProps<{
    product: Product;
    categories: Category[];
    brands: Brand[];
    availabilityOptions: AvailabilityOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Productos', href: productsIndex() },
            { title: 'Editar producto', href: productsIndex() },
        ],
    },
});
</script>

<template>
    <Head :title="`Editar ${product.name}`" />

    <div class="flex flex-col gap-5 p-4">
        <div>
            <Link
                :href="productsIndex()"
                class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-[#042163]"
            >
                <ArrowLeft class="size-3.5" />
                Volver al inventario
            </Link>
            <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1
                        class="flex items-center gap-2 text-xl font-bold text-slate-900"
                    >
                        <PackageOpen class="size-5 text-[#042163]" />
                        Editar producto
                    </h1>
                    <p
                        class="mt-1 font-mono text-xs tracking-wide text-slate-500 uppercase"
                    >
                        {{ product.sku }}
                    </p>
                </div>

                <Link
                    :href="catalogShow(product.slug)"
                    class="text-xs font-semibold text-[#c63427] hover:underline"
                >
                    Ver en el sitio público
                </Link>
            </div>
        </div>

        <ProductForm
            :product="product"
            :categories="categories"
            :brands="brands"
            :availability-options="availabilityOptions"
        />
    </div>
</template>
