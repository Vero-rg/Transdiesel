<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Clock,
    MapPin,
    Menu,
    MessageCircle,
    Phone,
    ShoppingCart,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import CartDrawer from '@/components/CartDrawer.vue';
import { Toaster } from '@/components/ui/sonner';
import { useCart } from '@/composables/useCart';
import { home, login } from '@/routes';
import { index as catalogIndex } from '@/routes/catalog';

const page = usePage();
const { totalItems, ready: cartReady, open: openCart } = useCart();

defineOptions({
    inheritAttrs: false,
});

const menuOpen = ref(false);

const isCatalog = computed(() => page.url.startsWith('/catalogo'));

const navigation = [
    { label: 'Inicio', href: home(), active: () => page.url === '/' },
    {
        label: 'Catálogo',
        href: catalogIndex(),
        active: () => isCatalog.value,
    },
    {
        label: 'Bombas',
        href: catalogIndex({ query: { categoria: 'bombas' } }),
        active: () => isCatalog.value && page.url.includes('categoria=bombas'),
    },
    {
        label: 'Inyectores',
        href: catalogIndex({ query: { categoria: 'inyectores' } }),
        active: () =>
            isCatalog.value && page.url.includes('categoria=inyectores'),
    },
];

const whatsappUrl = 'https://wa.me/523322544431';

const brandLogo = computed(() => page.props.brand.logo);
const brandLogoWhite = computed(() => page.props.brand.logo_white);
const footerLogo = computed(() => brandLogoWhite.value ?? brandLogo.value);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-slate-50 text-slate-800">
        <div class="bg-[#042163] text-white">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-2 text-xs sm:flex-row sm:items-center sm:justify-between"
            >
                <span class="flex items-center gap-2">
                    <MapPin class="size-3.5" />
                    Av. Tesistán 778, Arcos de Zapopan, 45130 Zapopan, Jal.
                </span>
                <span class="flex items-center gap-4">
                    <span class="flex items-center gap-2">
                        <Clock class="size-3.5" />
                        L-V 9:00-17:00 · Sáb 9:00-13:00
                    </span>
                    <a
                        href="tel:+523322544431"
                        class="flex items-center gap-2 font-semibold hover:text-[#f8b4ad]"
                    >
                        <Phone class="size-3.5" />
                        33 2254 4431
                    </a>
                </span>
            </div>
        </div>

        <header
            class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3"
            >
                <Link :href="home()" class="flex items-center gap-3">
                    <img
                        v-if="brandLogo"
                        :src="brandLogo"
                        alt="Transdiesel — Laboratorio de inyección y partes diésel"
                        class="h-20 w-auto sm:h-14"
                    />
                    <template v-else>
                        <span
                            class="flex size-11 items-center justify-center rounded-xl bg-[#042163] text-sm font-black tracking-tight text-white"
                        >
                            TD
                        </span>
                        <span class="leading-tight">
                            <span
                                class="block text-base font-extrabold tracking-tight text-[#042163] sm:text-lg"
                            >
                                TRANSDIESEL
                            </span>
                            <span
                                class="block text-[10px] font-medium tracking-[0.12em] text-[#c63427] uppercase"
                            >
                                Laboratorio de inyección y partes diésel
                            </span>
                        </span>
                    </template>
                </Link>

                <nav class="hidden items-center gap-1 lg:flex">
                    <Link
                        v-for="item in navigation"
                        :key="item.label"
                        :href="item.href"
                        :class="[
                            'rounded-lg px-3 py-2 text-sm font-semibold transition',
                            item.active()
                                ? 'bg-[#042163]/5 text-[#042163]'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-[#042163]',
                        ]"
                    >
                        {{ item.label }}
                    </Link>
                    <a
                        href="#contacto"
                        class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-[#042163]"
                    >
                        Contacto
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="relative flex size-10 items-center justify-center rounded-lg border border-slate-200 text-[#042163] transition hover:border-[#042163]/40"
                        aria-label="Abrir carrito"
                        @click="openCart"
                    >
                        <ShoppingCart class="size-5" />
                        <span
                            v-if="cartReady && totalItems > 0"
                            class="absolute -top-1.5 -right-1.5 flex size-5 items-center justify-center rounded-full bg-[#c63427] text-[10px] font-bold text-white"
                        >
                            {{ totalItems }}
                        </span>
                    </button>

                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden items-center gap-2 rounded-lg bg-[#c63427] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#a82b20] sm:flex"
                    >
                        <MessageCircle class="size-4" />
                        WhatsApp
                    </a>

                    <button
                        type="button"
                        class="flex size-10 items-center justify-center rounded-lg border border-slate-200 text-[#042163] lg:hidden"
                        aria-label="Abrir menú"
                        @click="menuOpen = !menuOpen"
                    >
                        <component :is="menuOpen ? X : Menu" class="size-5" />
                    </button>
                </div>
            </div>

            <div v-if="menuOpen" class="border-t border-slate-200 lg:hidden">
                <nav class="mx-auto flex max-w-7xl flex-col px-4 py-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.label"
                        :href="item.href"
                        class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        @click="menuOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                    <a
                        href="#contacto"
                        class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        @click="menuOpen = false"
                    >
                        Contacto
                    </a>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer id="contacto" class="mt-16 bg-[#042163] text-slate-200">
            <div
                class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:grid-cols-2 lg:grid-cols-4"
            >
                <div>
                    <Link
                        :href="login()"
                        class="inline-block"
                        aria-label="Ir a iniciar sesión"
                    >
                        <img
                            v-if="footerLogo"
                            :src="footerLogo"
                            alt="Transdiesel — Laboratorio de inyección y partes diésel"
                            :class="[
                                'h-16 w-auto',
                                brandLogoWhite ? '' : 'rounded-lg bg-white p-2',
                            ]"
                        />
                        <p v-else class="text-lg font-extrabold text-white">
                            TRANSDIESEL
                        </p>
                    </Link>
                    <p
                        class="mt-1 text-xs tracking-[0.12em] text-[#f8b4ad] uppercase"
                    >
                        Laboratorio de inyección y partes diésel
                    </p>
                    <p class="mt-4 text-sm leading-relaxed text-slate-300">
                        Especialistas en diagnóstico, reparación y calibración
                        de bombas e inyectores diésel en Zapopan, Jalisco.
                    </p>
                </div>

                <div>
                    <p class="text-sm font-bold text-white uppercase">
                        Contacto
                    </p>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <MapPin class="mt-0.5 size-4 shrink-0" />
                            Av. Tesistán 778, Arcos de Zapopan, 45130 Zapopan,
                            Jal.
                        </li>
                        <li class="flex items-center gap-2">
                            <Phone class="size-4 shrink-0" />
                            <a href="tel:+523322544431" class="hover:text-white"
                                >33 2254 4431</a
                            >
                        </li>
                        <li class="flex items-center gap-2">
                            <MessageCircle class="size-4 shrink-0" />
                            <a
                                :href="whatsappUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="hover:text-white"
                                >Cotiza por WhatsApp</a
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <p class="text-sm font-bold text-white uppercase">
                        Horarios
                    </p>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li class="flex justify-between gap-4">
                            <span>Lunes a viernes</span>
                            <span class="font-semibold text-white"
                                >9:00 - 17:00</span
                            >
                        </li>
                        <li class="flex justify-between gap-4">
                            <span>Sábado</span>
                            <span class="font-semibold text-white"
                                >9:00 - 13:00</span
                            >
                        </li>
                        <li class="flex justify-between gap-4">
                            <span>Domingo</span>
                            <span class="font-semibold text-[#f8b4ad]"
                                >Cerrado</span
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <p class="text-sm font-bold text-white uppercase">
                        Catálogo
                    </p>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li>
                            <Link
                                :href="
                                    catalogIndex({
                                        query: { categoria: 'bombas' },
                                    })
                                "
                                class="hover:text-white"
                                >Bombas de diésel</Link
                            >
                        </li>
                        <li>
                            <Link
                                :href="
                                    catalogIndex({
                                        query: { categoria: 'inyectores' },
                                    })
                                "
                                class="hover:text-white"
                                >Inyectores de diésel</Link
                            >
                        </li>
                        <li>
                            <Link
                                :href="catalogIndex()"
                                class="hover:text-white"
                                >Ver todo el catálogo</Link
                            >
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10">
                <div
                    class="mx-auto max-w-7xl px-4 py-4 text-center text-xs text-slate-400"
                >
                    © {{ new Date().getFullYear() }} TRANSDIESEL. Todos los
                    derechos reservados.
                </div>
            </div>
        </footer>

        <CartDrawer />
        <Toaster />
    </div>
</template>
