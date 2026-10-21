<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const logo = computed(
    () => page.props.brand.logo ?? '/images/logo-transdiesel.png',
);
</script>

<template>
    <img
        :src="logo"
        alt="Transdiesel"
        class="h-11 w-auto max-w-full rounded-md bg-white object-contain p-1 group-data-[collapsible=icon]:hidden"
    />
    <span
        class="hidden size-8 items-center justify-center rounded-md bg-sidebar-primary text-[10px] font-black tracking-tight text-sidebar-primary-foreground group-data-[collapsible=icon]:flex"
    >
        TD
    </span>
</template>
