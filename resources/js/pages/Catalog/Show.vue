<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ChevronRight,
    ImageOff,
    MessageCircle,
    Minus,
    Plus,
    ShieldCheck,
    ShoppingCart,
    Truck,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import AvailabilityBadge from '@/components/AvailabilityBadge.vue';
import ProductCard from '@/components/ProductCard.vue';
import StarRating from '@/components/StarRating.vue';
import { useCart } from '@/composables/useCart';
import { formatCurrency, formatDate, formatRating } from '@/lib/format';
import { home } from '@/routes';
import { index as catalogIndex } from '@/routes/catalog';
import { store as storeReview } from '@/routes/reviews';
import type { Product, Review } from '@/types';

const props = defineProps<{
    product: Product;
    reviews: Review[];
    relatedProducts: Product[];
    ratingAverage: number;
    reviewsCount: number;
}>();

const cart = useCart();
const quantity = ref(1);

const specifications = computed(() =>
    Object.entries(props.product.specifications ?? {}),
);

const whatsappUrl = computed(
    () =>
        `https://wa.me/523322544431?text=${encodeURIComponent(
            `Hola TRANSDIESEL, me interesa el producto ${props.product.name} (SKU ${props.product.sku}). ¿Está disponible?`,
        )}`,
);

function addToCart(): void {
    cart.addItem(props.product, quantity.value);
}

const form = useForm({
    author_name: '',
    author_email: '',
    rating: 0,
    comment: '',
});

function submitReview(): void {
    form.post(storeReview(props.product.slug).url, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head :title="product.name" />

    <div class="mx-auto max-w-7xl px-4 py-6">
        <nav
            class="flex flex-wrap items-center gap-1 text-xs text-slate-500"
            aria-label="Ruta de navegación"
        >
            <Link :href="home()" class="hover:text-[#042163]">Inicio</Link>
            <ChevronRight class="size-3" />
            <Link :href="catalogIndex()" class="hover:text-[#042163]">
                Catálogo
            </Link>
            <ChevronRight class="size-3" />
            <Link
                v-if="product.category"
                :href="
                    catalogIndex({
                        query: { categoria: product.category.slug },
                    })
                "
                class="hover:text-[#042163]"
            >
                {{ product.category.name }}
            </Link>
            <ChevronRight v-if="product.category" class="size-3" />
            <span class="font-semibold text-[#042163]">{{ product.sku }}</span>
        </nav>

        <div class="mt-6 grid gap-8 lg:grid-cols-2">
            <div>
                <div
                    class="relative aspect-square overflow-hidden rounded-3xl border border-slate-200 bg-white"
                >
                    <img
                        v-if="product.image_url"
                        :src="product.image_url"
                        :alt="product.name"
                        class="size-full object-cover"
                    />
                    <div
                        v-else
                        class="flex size-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400"
                    >
                        <ImageOff class="size-14" />
                        <span class="text-sm">Imagen no disponible</span>
                    </div>

                    <span
                        class="absolute top-4 left-4 rounded-full bg-[#c63427] px-3 py-1 text-xs font-bold tracking-wide text-white uppercase"
                    >
                        {{ product.brand?.name ?? 'Genérico' }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div
                        class="flex aspect-square items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400"
                    >
                        <ShieldCheck class="size-6" />
                    </div>
                    <div
                        class="flex aspect-square items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400"
                    >
                        <Truck class="size-6" />
                    </div>
                    <div
                        class="flex aspect-square items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400"
                    >
                        <MessageCircle class="size-6" />
                    </div>
                </div>
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <AvailabilityBadge :availability="product.availability" />
                    <span
                        class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600"
                    >
                        {{ product.category?.name }}
                    </span>
                </div>

                <h1
                    class="mt-3 text-2xl leading-tight font-black tracking-tight text-[#042163] sm:text-3xl"
                >
                    {{ product.name }}
                </h1>

                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                    <div class="flex items-center gap-1.5">
                        <StarRating :model-value="ratingAverage" size="sm" />
                        <span class="font-semibold text-[#042163]">
                            {{ formatRating(ratingAverage) }}
                        </span>
                        <span class="text-slate-500">
                            ({{ reviewsCount }}
                            {{ reviewsCount === 1 ? 'reseña' : 'reseñas' }})
                        </span>
                    </div>
                    <span
                        class="font-mono text-xs tracking-wide text-slate-500 uppercase"
                    >
                        SKU: {{ product.sku }}
                    </span>
                </div>

                <div
                    class="mt-5 rounded-2xl border border-slate-200 bg-white p-5"
                >
                    <p class="text-xs font-semibold text-slate-500 uppercase">
                        Precio
                    </p>
                    <p class="text-3xl font-black text-[#042163]">
                        {{ formatCurrency(product.price) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Precio en MXN, sujeto a confirmación de disponibilidad.
                    </p>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <div
                            class="flex items-center rounded-xl border border-slate-200"
                        >
                            <button
                                type="button"
                                class="flex size-10 items-center justify-center text-slate-500 transition hover:text-[#c63427]"
                                aria-label="Disminuir cantidad"
                                @click="quantity = Math.max(1, quantity - 1)"
                            >
                                <Minus class="size-4" />
                            </button>
                            <span
                                class="w-10 text-center text-sm font-bold text-slate-700"
                            >
                                {{ quantity }}
                            </span>
                            <button
                                type="button"
                                class="flex size-10 items-center justify-center text-slate-500 transition hover:text-[#042163]"
                                aria-label="Aumentar cantidad"
                                @click="quantity = Math.min(99, quantity + 1)"
                            >
                                <Plus class="size-4" />
                            </button>
                        </div>

                        <button
                            type="button"
                            class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-[#042163] px-5 text-sm font-bold text-white transition hover:bg-[#042163]/90 sm:flex-none"
                            @click="addToCart"
                        >
                            <ShoppingCart class="size-4" />
                            Agregar al carrito
                        </button>

                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-[#25D366] px-5 text-sm font-bold text-white transition hover:bg-[#1ebe5b] sm:flex-none"
                        >
                            <MessageCircle class="size-4" />
                            WhatsApp
                        </a>
                    </div>
                </div>

                <div v-if="product.description" class="mt-6">
                    <h2
                        class="text-sm font-bold tracking-wide text-[#042163] uppercase"
                    >
                        Descripción
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        {{ product.description }}
                    </p>
                </div>

                <div v-if="specifications.length > 0" class="mt-6">
                    <h2
                        class="text-sm font-bold tracking-wide text-[#042163] uppercase"
                    >
                        Ficha técnica
                    </h2>
                    <dl
                        class="mt-3 divide-y divide-slate-200 overflow-hidden rounded-xl border border-slate-200 bg-white"
                    >
                        <div
                            v-for="[key, value] in specifications"
                            :key="key"
                            class="flex items-center justify-between gap-4 px-4 py-3 text-sm"
                        >
                            <dt class="text-slate-500">{{ key }}</dt>
                            <dd class="text-right font-semibold text-[#042163]">
                                {{ value }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <section class="mt-14" aria-labelledby="reviews-title">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2
                        id="reviews-title"
                        class="text-2xl font-black tracking-tight text-[#042163]"
                    >
                        Reseñas y valoraciones
                    </h2>
                    <div class="mt-2 flex items-center gap-2">
                        <StarRating :model-value="ratingAverage" size="md" />
                        <span class="text-sm font-semibold text-[#042163]">
                            {{ formatRating(ratingAverage) }} de 5
                        </span>
                        <span class="text-sm text-slate-500">
                            · {{ reviewsCount }} reseñas aprobadas
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_420px]">
                <div>
                    <ul
                        v-if="reviews.length > 0"
                        class="divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white"
                    >
                        <li
                            v-for="review in reviews"
                            :key="review.id"
                            class="px-5 py-4"
                        >
                            <div
                                class="flex flex-wrap items-center justify-between gap-2"
                            >
                                <p class="font-semibold text-[#042163]">
                                    {{ review.author_name }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ formatDate(review.created_at) }}
                                </p>
                            </div>
                            <StarRating
                                :model-value="review.rating"
                                size="sm"
                                class="mt-1"
                            />
                            <p
                                v-if="review.comment"
                                class="mt-2 text-sm leading-relaxed text-slate-600"
                            >
                                {{ review.comment }}
                            </p>
                        </li>
                    </ul>

                    <p
                        v-else
                        class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center text-sm text-slate-500"
                    >
                        Este producto aún no tiene reseñas. ¡Sé el primero en
                        opinar!
                    </p>
                </div>

                <form
                    class="rounded-2xl border border-slate-200 bg-white p-5"
                    @submit.prevent="submitReview"
                >
                    <h3 class="font-bold text-[#042163]">Escribe tu reseña</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Tu opinión se publicará después de ser revisada.
                    </p>

                    <div class="mt-4 grid gap-4">
                        <div>
                            <label
                                for="author_name"
                                class="text-xs font-semibold text-slate-600"
                            >
                                Nombre
                            </label>
                            <input
                                id="author_name"
                                v-model="form.author_name"
                                type="text"
                                class="mt-1 h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                                placeholder="Tu nombre"
                            />
                            <p
                                v-if="form.errors.author_name"
                                class="mt-1 text-xs text-[#c63427]"
                            >
                                {{ form.errors.author_name }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="author_email"
                                class="text-xs font-semibold text-slate-600"
                            >
                                Correo electrónico
                            </label>
                            <input
                                id="author_email"
                                v-model="form.author_email"
                                type="email"
                                class="mt-1 h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                                placeholder="tucorreo@ejemplo.com"
                            />
                            <p
                                v-if="form.errors.author_email"
                                class="mt-1 text-xs text-[#c63427]"
                            >
                                {{ form.errors.author_email }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-slate-600">
                                Calificación
                            </span>
                            <StarRating
                                v-model="form.rating"
                                :readonly="false"
                                size="lg"
                                class="mt-1"
                            />
                            <p
                                v-if="form.errors.rating"
                                class="mt-1 text-xs text-[#c63427]"
                            >
                                {{ form.errors.rating }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="comment"
                                class="text-xs font-semibold text-slate-600"
                            >
                                Comentario
                            </label>
                            <textarea
                                id="comment"
                                v-model="form.comment"
                                rows="4"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                                placeholder="Cuéntanos tu experiencia con este producto"
                            />
                            <p
                                v-if="form.errors.comment"
                                class="mt-1 text-xs text-[#c63427]"
                            >
                                {{ form.errors.comment }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="h-11 rounded-xl bg-[#c63427] text-sm font-bold text-white transition hover:bg-[#a82b20] disabled:opacity-60"
                        >
                            {{
                                form.processing ? 'Enviando…' : 'Enviar reseña'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section v-if="relatedProducts.length > 0" class="mt-14">
            <h2 class="text-2xl font-black tracking-tight text-[#042163]">
                Productos relacionados
            </h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard
                    v-for="related in relatedProducts"
                    :key="related.id"
                    :product="related"
                />
            </div>
        </section>

        <div class="mt-10">
            <Link
                :href="catalogIndex()"
                class="text-sm font-semibold text-[#c63427] hover:underline"
            >
                ← Volver al catálogo
            </Link>
        </div>
    </div>
</template>
