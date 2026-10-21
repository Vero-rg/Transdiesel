<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ImageOff, ShoppingCart } from '@lucide/vue';
import AvailabilityBadge from '@/components/AvailabilityBadge.vue';
import { Button } from '@/components/ui/button';
import { useCart } from '@/composables/useCart';
import { formatCurrency } from '@/lib/format';
import { show } from '@/routes/catalog';
import type { Product } from '@/types';

const props = defineProps<{
    product: Product;
}>();

const cart = useCart();

function addToCart(): void {
    cart.addItem(props.product);
}
</script>

<template>
    <article
        class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-[#042163]/30 hover:shadow-xl"
    >
        <Link
            :href="show(product.slug)"
            class="relative block aspect-4/3 overflow-hidden bg-slate-100"
        >
            <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="size-full object-contain transition duration-500 group-hover:scale-105"
                loading="lazy"
            />
            <div
                v-else
                class="flex size-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400"
            >
                <ImageOff class="size-10" />
            </div>

            <span
                class="absolute top-3 left-3 rounded-full bg-[#042163] px-2.5 py-1 text-[11px] font-semibold tracking-wide text-white uppercase"
            >
                {{ product.brand?.name ?? 'Genérico' }}
            </span>
        </Link>

        <div class="flex flex-1 flex-col gap-3 p-5">
            <div class="flex items-center justify-between gap-2">
                <p
                    class="font-mono text-xs tracking-wide text-slate-500 uppercase"
                >
                    {{ product.sku }}
                </p>
                <AvailabilityBadge :availability="product.availability" />
            </div>

            <h3
                class="line-clamp-2 text-base leading-snug font-semibold text-[#042163]"
            >
                <Link :href="show(product.slug)" class="hover:underline">
                    {{ product.name }}
                </Link>
            </h3>

            <p class="text-xs font-medium text-[#c63427]">
                {{ product.category?.name }}
            </p>

            <div class="mt-auto flex items-end justify-between gap-3 pt-2">
                <div>
                    <p class="text-[11px] text-slate-500">Precio</p>
                    <p class="text-lg font-bold text-[#042163]">
                        {{ formatCurrency(product.price) }}
                    </p>
                </div>

                <Button
                    type="button"
                    size="sm"
                    class="bg-[#042163] text-white hover:bg-[#042163]/90"
                    @click="addToCart"
                >
                    <ShoppingCart class="size-4" />
                    Agregar
                </Button>
            </div>

            <Link
                :href="show(product.slug)"
                class="text-xs font-semibold text-[#c63427] underline-offset-4 hover:underline"
            >
                Ver detalle del producto
            </Link>
        </div>
    </article>
</template>
