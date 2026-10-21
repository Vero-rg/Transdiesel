<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, PackagePlus } from '@lucide/vue';
import ProductForm from '@/components/admin/ProductForm.vue';
import {
    create as createProduct,
    index as productsIndex,
} from '@/routes/admin/products';
import type { AvailabilityOption, Brand, Category } from '@/types';

defineProps<{
    categories: Category[];
    brands: Brand[];
    availabilityOptions: AvailabilityOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Productos', href: productsIndex() },
            { title: 'Nuevo producto', href: createProduct() },
        ],
    },
});
</script>

<template>
    <Head title="Nuevo producto" />

    <div class="flex flex-col gap-5 p-4">
        <div>
            <Link
                :href="productsIndex()"
                class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-[#042163]"
            >
                <ArrowLeft class="size-3.5" />
                Volver al inventario
            </Link>
            <h1
                class="mt-2 flex items-center gap-2 text-xl font-bold text-slate-900"
            >
                <PackagePlus class="size-5 text-[#042163]" />
                Alta de producto
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Captura los datos del producto, su ficha técnica y una imagen.
            </p>
        </div>

        <ProductForm
            :categories="categories"
            :brands="brands"
            :availability-options="availabilityOptions"
        />
    </div>
</template>
