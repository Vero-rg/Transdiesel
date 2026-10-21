<script setup lang="ts">
import { Star } from '@lucide/vue';
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        modelValue?: number;
        readonly?: boolean;
        size?: 'sm' | 'md' | 'lg';
        class?: string;
    }>(),
    {
        modelValue: 0,
        readonly: true,
        size: 'sm',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: number];
}>();

const sizeClasses = {
    sm: 'size-4',
    md: 'size-5',
    lg: 'size-7',
} as const;

const rating = computed(() => Math.round(props.modelValue));

function select(value: number): void {
    if (!props.readonly) {
        emit('update:modelValue', value);
    }
}
</script>

<template>
    <div :class="cn('flex items-center gap-0.5', props.class)">
        <component
            :is="readonly ? 'span' : 'button'"
            v-for="star in 5"
            :key="star"
            :type="readonly ? undefined : 'button'"
            :aria-label="
                readonly ? undefined : `Calificar con ${star} estrellas`
            "
            :class="
                cn(
                    'inline-flex items-center justify-center transition',
                    !readonly && 'cursor-pointer hover:scale-110',
                )
            "
            @click="select(star)"
        >
            <Star
                :class="
                    cn(
                        sizeClasses[size],
                        star <= rating
                            ? 'fill-[#c63427] text-[#c63427]'
                            : 'fill-transparent text-slate-300',
                    )
                "
            />
        </component>
    </div>
</template>
