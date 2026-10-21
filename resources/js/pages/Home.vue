<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    BadgeCheck,
    Clock,
    Gauge,
    MapPin,
    MessageCircle,
    Phone,
    ShieldCheck,
    Truck,
    Wrench,
} from '@lucide/vue';
import ProductCard from '@/components/ProductCard.vue';
import { index as catalogIndex } from '@/routes/catalog';
import type { Brand, Category, Product } from '@/types';

defineProps<{
    categories: Category[];
    brands: Brand[];
    featuredProducts: Product[];
    stats: { products: number; brands: number };
}>();

const whatsappUrl = 'https://wa.me/523322544431';

const categoryMeta: Record<
    string,
    { icon: typeof Wrench; description: string; accent: string }
> = {
    bombas: {
        icon: Wrench,
        description:
            'Bombas rotativas, en línea y de alta presión reconstruidas y calibradas en banco de pruebas.',
        accent: 'from-[#042163] to-[#0a3a9c]',
    },
    inyectores: {
        icon: Gauge,
        description:
            'Inyectores common rail, electrónicos y piezoeléctricos con prueba de caudal y retorno.',
        accent: 'from-[#c63427] to-[#8f231a]',
    },
};

const services = [
    {
        icon: Gauge,
        title: 'Diagnóstico especializado',
        text: 'Escaneo y pruebas en banco para bombas e inyectores diésel.',
    },
    {
        icon: Wrench,
        title: 'Reparación y calibración',
        text: 'Reconstrucción con refacciones originales y certificado de calibración.',
    },
    {
        icon: ShieldCheck,
        title: 'Garantía respaldada',
        text: 'Todos nuestros trabajos incluyen 6 meses de garantía.',
    },
    {
        icon: Truck,
        title: 'Envíos a todo México',
        text: 'Coordinamos la logística para que tu unidad vuelva a operar.',
    },
];

function initials(name: string): string {
    return name.slice(0, 2).toUpperCase();
}
</script>

<template>
    <Head title="Laboratorio Especializado en Inyección Diésel" />

    <section
        class="relative overflow-hidden bg-[#042163] text-white"
        aria-labelledby="hero-title"
    >
        <div
            class="pointer-events-none absolute inset-0 opacity-40"
            style="
                background-image:
                    radial-gradient(
                        circle at 15% 20%,
                        rgba(198, 52, 39, 0.45),
                        transparent 45%
                    ),
                    radial-gradient(
                        circle at 85% 10%,
                        rgba(255, 255, 255, 0.18),
                        transparent 40%
                    );
            "
        />
        <div
            class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 lg:grid-cols-[1.15fr_1fr] lg:py-24"
        >
            <div>
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold tracking-wider uppercase"
                >
                    <MapPin class="size-3.5" />
                    Zapopan, Jalisco
                </span>

                <h1
                    id="hero-title"
                    class="mt-6 text-4xl leading-tight font-black tracking-tight sm:text-5xl lg:text-6xl"
                >
                    Laboratorio Especializado en
                    <span class="text-[#f8b4ad]">Inyección Diésel</span>
                </h1>

                <p
                    class="mt-5 max-w-xl text-base leading-relaxed text-slate-200 sm:text-lg"
                >
                    Reconstrucción, calibración y venta de bombas e inyectores
                    diésel para Amarok, Cummins, Ford, Caterpillar, Navistar,
                    Bosch y Denso. Más de
                    {{ stats.products }} refacciones disponibles con garantía de
                    6 meses.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#c63427] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-black/20 transition hover:bg-[#a82b20]"
                    >
                        <MessageCircle class="size-5" />
                        Cotiza por WhatsApp
                    </a>
                    <Link
                        :href="catalogIndex()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/5 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-white/15"
                    >
                        Ver catálogo
                        <ArrowRight class="size-4" />
                    </Link>
                </div>

                <dl class="mt-10 grid max-w-md grid-cols-3 gap-4">
                    <div>
                        <dt
                            class="text-xs tracking-wide text-slate-300 uppercase"
                        >
                            Refacciones
                        </dt>
                        <dd class="text-2xl font-black">
                            {{ stats.products }}+
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs tracking-wide text-slate-300 uppercase"
                        >
                            Marcas
                        </dt>
                        <dd class="text-2xl font-black">
                            {{ stats.brands }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs tracking-wide text-slate-300 uppercase"
                        >
                            Garantía
                        </dt>
                        <dd class="text-2xl font-black">6 meses</dd>
                    </div>
                </dl>
            </div>

            <div class="relative">
                <div
                    class="rounded-3xl border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-sm"
                >
                    <p
                        class="text-xs font-semibold tracking-[0.16em] text-[#f8b4ad] uppercase"
                    >
                        Atención directa
                    </p>
                    <p class="mt-2 text-2xl font-bold">33 2254 4431</p>
                    <p class="mt-1 text-sm text-slate-200">
                        Av. Tesistán 778, Arcos de Zapopan
                    </p>

                    <div
                        class="mt-6 space-y-3 border-t border-white/15 pt-5 text-sm"
                    >
                        <p class="flex items-center gap-3">
                            <Clock class="size-4 text-[#f8b4ad]" />
                            L-V 9:00-17:00 · Sáb 9:00-13:00
                        </p>
                        <p class="flex items-center gap-3">
                            <BadgeCheck class="size-4 text-[#f8b4ad]" />
                            Diagnóstico y calibración en banco
                        </p>
                        <p class="flex items-center gap-3">
                            <ShieldCheck class="size-4 text-[#f8b4ad]" />
                            Refacciones originales y reconstruidas
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        class="mx-auto max-w-7xl px-4 py-16"
        aria-labelledby="categories-title"
    >
        <div
            class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="text-xs font-bold tracking-[0.16em] text-[#c63427] uppercase"
                >
                    Nuestro catálogo
                </p>
                <h2
                    id="categories-title"
                    class="mt-2 text-3xl font-black tracking-tight text-[#042163]"
                >
                    Encuentra tu refacción
                </h2>
            </div>
            <Link
                :href="catalogIndex()"
                class="text-sm font-semibold text-[#c63427] underline-offset-4 hover:underline"
            >
                Ver catálogo completo
            </Link>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <Link
                v-for="category in categories"
                :key="category.id"
                :href="catalogIndex({ query: { categoria: category.slug } })"
                class="group relative overflow-hidden rounded-3xl p-8 text-white shadow-lg transition hover:-translate-y-1 hover:shadow-2xl"
                :class="`bg-gradient-to-br ${categoryMeta[category.slug]?.accent ?? 'from-[#042163] to-[#0a3a9c]'}`"
            >
                <component
                    :is="categoryMeta[category.slug]?.icon ?? Wrench"
                    class="size-10 text-white/80"
                />
                <h3 class="mt-6 text-2xl font-black tracking-tight">
                    {{ category.name }}
                </h3>
                <p class="mt-2 max-w-sm text-sm leading-relaxed text-white/85">
                    {{
                        categoryMeta[category.slug]?.description ??
                        'Refacciones diésel con garantía y respaldo técnico.'
                    }}
                </p>
                <div class="mt-8 flex items-center justify-between">
                    <span
                        class="text-xs font-semibold tracking-wide text-white/80 uppercase"
                    >
                        {{ category.products_count ?? 0 }} productos
                    </span>
                    <span
                        class="inline-flex items-center gap-2 text-sm font-bold transition group-hover:gap-3"
                    >
                        Explorar
                        <ArrowRight class="size-4" />
                    </span>
                </div>
            </Link>
        </div>
    </section>

    <section class="bg-white py-16" aria-labelledby="brands-title">
        <div class="mx-auto max-w-7xl px-4">
            <div class="text-center">
                <p
                    class="text-xs font-bold tracking-[0.16em] text-[#c63427] uppercase"
                >
                    Especialistas en inyección
                </p>
                <h2
                    id="brands-title"
                    class="mt-2 text-3xl font-black tracking-tight text-[#042163]"
                >
                    Marcas que atendemos
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-600">
                    Trabajamos con las principales marcas de bombas e inyectores
                    diésel del mercado.
                </p>
            </div>

            <ul
                class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-7"
            >
                <li v-for="brand in brands" :key="brand.id">
                    <Link
                        :href="catalogIndex({ query: { marca: brand.slug } })"
                        class="group flex h-28 flex-col items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 text-center transition hover:-translate-y-1 hover:border-[#042163]/30 hover:bg-white hover:shadow-lg"
                    >
                        <img
                            v-if="brand.logo_url"
                            :src="brand.logo_url"
                            :alt="brand.name"
                            class="max-h-10 max-w-[80%] object-contain"
                            loading="lazy"
                        />
                        <span
                            v-else
                            class="flex size-11 items-center justify-center rounded-xl bg-[#042163] text-sm font-black text-white"
                        >
                            {{ initials(brand.name) }}
                        </span>
                        <span
                            class="text-xs font-bold tracking-wide text-[#042163] uppercase"
                        >
                            {{ brand.name }}
                        </span>
                    </Link>
                </li>
            </ul>
        </div>
    </section>

    <section
        v-if="featuredProducts.length > 0"
        class="mx-auto max-w-7xl px-4 py-16"
        aria-labelledby="featured-title"
    >
        <div
            class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="text-xs font-bold tracking-[0.16em] text-[#c63427] uppercase"
                >
                    Disponibles ahora
                </p>
                <h2
                    id="featured-title"
                    class="mt-2 text-3xl font-black tracking-tight text-[#042163]"
                >
                    Productos destacados
                </h2>
            </div>
            <Link
                :href="catalogIndex()"
                class="text-sm font-semibold text-[#c63427] underline-offset-4 hover:underline"
            >
                Ver todo
            </Link>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <ProductCard
                v-for="product in featuredProducts"
                :key="product.id"
                :product="product"
            />
        </div>
    </section>

    <section class="bg-white py-16" aria-labelledby="services-title">
        <div class="mx-auto max-w-7xl px-4">
            <div class="text-center">
                <p
                    class="text-xs font-bold tracking-[0.16em] text-[#c63427] uppercase"
                >
                    Nuestros servicios
                </p>
                <h2
                    id="services-title"
                    class="mt-2 text-3xl font-black tracking-tight text-[#042163]"
                >
                    Servicio integral para tu unidad
                </h2>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="service in services"
                    :key="service.title"
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-6 transition hover:border-[#042163]/25 hover:bg-white hover:shadow-md"
                >
                    <span
                        class="flex size-11 items-center justify-center rounded-xl bg-[#042163]/10 text-[#042163]"
                    >
                        <component :is="service.icon" class="size-5" />
                    </span>
                    <h3 class="mt-4 font-bold text-[#042163]">
                        {{ service.title }}
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        {{ service.text }}
                    </p>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center gap-2 text-[#042163]">
                        <MapPin class="size-5" />
                        <h3 class="font-bold">Ubicación</h3>
                    </div>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Av. Tesistán 778, Arcos de Zapopan, 45130 Zapopan, Jal.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center gap-2 text-[#042163]">
                        <Clock class="size-5" />
                        <h3 class="font-bold">Horarios</h3>
                    </div>
                    <ul class="mt-3 space-y-1 text-sm text-slate-600">
                        <li>Lunes a viernes: 9:00 - 17:00</li>
                        <li>Sábado: 9:00 - 13:00</li>
                        <li>Domingo: cerrado</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center gap-2 text-[#042163]">
                        <Phone class="size-5" />
                        <h3 class="font-bold">Contacto</h3>
                    </div>
                    <p class="mt-3 text-sm text-slate-600">
                        Teléfono y WhatsApp:
                        <a
                            href="tel:+523322544431"
                            class="font-semibold text-[#c63427] hover:underline"
                        >
                            33 2254 4431
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-4">
        <div
            class="flex flex-col items-center justify-between gap-6 rounded-3xl bg-[#042163] px-8 py-10 text-center text-white sm:flex-row sm:text-left"
        >
            <div>
                <h2 class="text-2xl font-black tracking-tight">
                    ¿No encuentras tu refacción?
                </h2>
                <p class="mt-2 text-sm text-slate-200">
                    Escríbenos con el SKU o número de parte y te cotizamos en
                    minutos.
                </p>
            </div>
            <a
                :href="whatsappUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-[#c63427] px-6 py-3.5 text-sm font-bold text-white transition hover:bg-[#a82b20]"
            >
                <MessageCircle class="size-5" />
                Solicitar cotización
            </a>
        </div>
    </section>
</template>
