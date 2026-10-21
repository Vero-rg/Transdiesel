<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Package, Tags } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as brandsIndex } from '@/routes/admin/brands';
import { index as productsIndex } from '@/routes/admin/products';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();

const isAdmin = computed(() => page.props.auth?.user?.is_admin === true);

// El Dashboard quedó oculto (legacy). Su vista vive en pages/legacy/Dashboard.vue.
const mainNavItems: NavItem[] = [];

const adminNavItems: NavItem[] = [
    {
        title: 'Productos',
        href: productsIndex(),
        icon: Package,
    },
    {
        title: 'Marcas',
        href: brandsIndex(),
        icon: Tags,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain v-if="mainNavItems.length" :items="mainNavItems" />
            <NavMain
                v-if="isAdmin"
                :items="adminNavItems"
                label="Administración"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
