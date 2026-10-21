<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import type { AvailabilityValue } from '@/types';

const props = defineProps<{
    availability: AvailabilityValue;
    class?: string;
}>();

const variants: Record<
    AvailabilityValue,
    { label: string; badge: string; dot: string }
> = {
    in_stock: {
        label: 'En stock',
        badge: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        dot: 'bg-emerald-500',
    },
    out_of_stock: {
        label: 'Agotado',
        badge: 'bg-red-50 text-[#c63427] ring-red-200',
        dot: 'bg-[#c63427]',
    },
    on_demand: {
        label: 'Bajo pedido',
        badge: 'bg-amber-50 text-amber-700 ring-amber-200',
        dot: 'bg-amber-500',
    },
};

const current = computed(
    () => variants[props.availability] ?? variants.in_stock,
);
</script>

<template>
    <span
        :class="
            cn(
                'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset',
                current.badge,
                props.class,
            )
        "
    >
        <span :class="cn('size-1.5 rounded-full', current.dot)" />
        {{ current.label }}
    </span>
</template>
