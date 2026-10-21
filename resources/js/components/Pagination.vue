<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { cn } from '@/lib/utils';
import type { PaginationLink } from '@/types';

defineProps<{
    links: PaginationLink[];
}>();

const labels: Record<string, string> = {
    '&laquo; Previous': 'Anterior',
    'Next &raquo;': 'Siguiente',
};

function label(text: string): string {
    return labels[text] ?? text;
}
</script>

<template>
    <nav
        v-if="links.length > 3"
        class="flex flex-wrap items-center justify-center gap-1.5"
        aria-label="Paginación"
    >
        <template v-for="(link, index) in links" :key="index">
            <Link
                v-if="link.url"
                :href="link.url"
                preserve-scroll
                :class="
                    cn(
                        'inline-flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm font-medium transition',
                        link.active
                            ? 'border-[#042163] bg-[#042163] text-white'
                            : 'border-slate-200 bg-white text-slate-600 hover:border-[#042163]/40 hover:text-[#042163]',
                    )
                "
            >
                <span v-html="label(link.label)" />
            </Link>
            <span
                v-else
                class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 text-sm text-slate-400"
            >
                <span v-html="label(link.label)" />
            </span>
        </template>
    </nav>
</template>
