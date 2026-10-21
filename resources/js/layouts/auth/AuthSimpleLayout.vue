<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BadgeCheck, Gauge, ShieldCheck } from '@lucide/vue';
import { computed } from 'vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const page = usePage();

const logo = computed(
    () => page.props.brand.logo ?? '/images/logo-transdiesel.png',
);
const logoWhite = computed(() => page.props.brand.logo_white ?? logo.value);
const year = new Date().getFullYear();

const highlights = [
    { icon: Gauge, text: 'Diagnóstico y calibración en banco de pruebas' },
    { icon: ShieldCheck, text: 'Refacciones originales y garantía de 6 meses' },
    { icon: BadgeCheck, text: 'Envíos y asesoría técnica a todo México' },
];
</script>

<template>
    <div
        class="grid min-h-svh bg-slate-50 lg:grid-cols-[1.05fr_1fr] dark:bg-background"
    >
        <aside
            class="relative hidden overflow-hidden bg-[#042163] px-12 py-12 text-white lg:flex lg:flex-col"
        >
            <div
                class="pointer-events-none absolute inset-0 opacity-60"
                style="
                    background-image:
                        radial-gradient(
                            circle at 18% 12%,
                            rgba(198, 52, 39, 0.55),
                            transparent 45%
                        ),
                        radial-gradient(
                            circle at 85% 85%,
                            rgba(10, 58, 156, 0.85),
                            transparent 52%
                        );
                "
            />
            <div
                class="pointer-events-none absolute inset-0 opacity-[0.06]"
                style="
                    background-image:
                        linear-gradient(
                            rgba(255, 255, 255, 0.7) 1px,
                            transparent 1px
                        ),
                        linear-gradient(
                            90deg,
                            rgba(255, 255, 255, 0.7) 1px,
                            transparent 1px
                        );
                    background-size: 56px 56px;
                "
            />

            <Link
                :href="home()"
                class="relative z-10 w-fit"
                aria-label="Transdiesel — ir al inicio"
            >
                <span
                    class="inline-flex rounded-2xl bg-white p-3 shadow-xl shadow-black/25"
                >
                    <img
                        :src="logoWhite"
                        alt="Transdiesel — Laboratorio de inyección y partes diésel"
                        class="h-16 w-auto object-contain sm:h-20"
                    />
                </span>
            </Link>

            <div class="relative z-10 mt-auto">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-semibold tracking-[0.14em] uppercase"
                >
                    Acceso privado
                </span>
                <h2
                    class="mt-5 text-3xl leading-tight font-black tracking-tight"
                >
                    Laboratorio de inyección
                    <span class="text-[#f8b4ad]">diésel</span>
                </h2>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-200">
                    Administra el catálogo de productos, marcas y disponibilidad
                    desde un solo lugar.
                </p>

                <ul class="mt-8 space-y-4 text-sm text-slate-100">
                    <li
                        v-for="item in highlights"
                        :key="item.text"
                        class="flex items-start gap-3"
                    >
                        <span
                            class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-[#f8b4ad]"
                        >
                            <component :is="item.icon" class="size-4" />
                        </span>
                        {{ item.text }}
                    </li>
                </ul>
            </div>

            <p
                class="relative z-10 mt-10 border-t border-white/10 pt-6 text-xs text-slate-300"
            >
                Av. Tesistán 778, Arcos de Zapopan, 45130 Zapopan, Jal.
            </p>
        </aside>

        <main class="flex items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-6 flex justify-center lg:hidden">
                    <Link
                        :href="home()"
                        class="inline-flex rounded-2xl bg-white p-2 shadow-lg ring-1 shadow-[#042163]/10 ring-slate-200"
                        aria-label="Transdiesel — ir al inicio"
                    >
                        <img
                            :src="logo"
                            alt="Transdiesel"
                            class="h-14 w-auto object-contain"
                        />
                    </Link>
                </div>

                <div
                    class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-[#042163]/5 sm:p-9 dark:border-border dark:bg-card dark:shadow-none"
                >
                    <div class="space-y-2">
                        <p
                            class="text-[11px] font-bold tracking-[0.18em] text-[#c63427] uppercase"
                        >
                            Panel Transdiesel
                        </p>
                        <h1
                            class="text-2xl font-black tracking-tight text-[#042163] dark:text-foreground"
                        >
                            {{ title }}
                        </h1>
                        <p
                            class="text-sm leading-relaxed text-slate-500 dark:text-muted-foreground"
                        >
                            {{ description }}
                        </p>
                    </div>

                    <div class="mt-7">
                        <slot />
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-slate-500">
                    © {{ year }} Transdiesel ·
                    <Link
                        :href="home()"
                        class="font-semibold text-[#042163] hover:text-[#c63427] dark:text-foreground"
                    >
                        Volver al sitio
                    </Link>
                </p>
            </div>
        </main>
    </div>
</template>
