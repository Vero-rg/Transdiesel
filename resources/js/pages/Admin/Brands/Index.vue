<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Tags, Trash2, Upload } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    destroy as destroyBrand,
    index as brandsIndex,
    status as statusBrand,
    store as storeBrand,
    update as updateBrand,
} from '@/routes/admin/brands';
import type { Brand } from '@/types';

defineProps<{
    brands: Brand[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Marcas',
                href: brandsIndex(),
            },
        ],
    },
});

const dialogOpen = ref(false);
const editing = ref<Brand | null>(null);
const brandToDelete = ref<Brand | null>(null);
const logoPreview = ref<string | null>(null);

const form = useForm({
    name: '',
    slug: '',
    is_active: true as boolean,
    logo: null as File | null,
});

function openCreate(): void {
    editing.value = null;
    form.reset();
    form.clearErrors();
    logoPreview.value = null;
    dialogOpen.value = true;
}

function openEdit(brand: Brand): void {
    editing.value = brand;
    form.clearErrors();
    form.name = brand.name;
    form.slug = brand.slug;
    form.is_active = brand.is_active ?? true;
    form.logo = null;
    logoPreview.value = brand.logo_url;
    dialogOpen.value = true;
}

function onLogoChange(event: Event): void {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    form.logo = file;

    if (file) {
        logoPreview.value = URL.createObjectURL(file);
    }
}

function submit(): void {
    const options = {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            dialogOpen.value = false;
            editing.value = null;
            form.reset();
        },
    };

    if (editing.value) {
        form.patch(updateBrand(editing.value.id).url, options);
    } else {
        form.post(storeBrand().url, options);
    }
}

function toggleStatus(brand: Brand): void {
    router.patch(
        statusBrand(brand.id).url,
        { is_active: !brand.is_active },
        { preserveScroll: true },
    );
}

function confirmDelete(): void {
    if (!brandToDelete.value) return;

    router.delete(destroyBrand(brandToDelete.value.id).url, {
        preserveScroll: true,
        onFinish: () => (brandToDelete.value = null),
    });
}

function initials(name: string): string {
    return name.slice(0, 2).toUpperCase();
}
</script>

<template>
    <Head title="Marcas" />

    <div class="flex flex-col gap-5 p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1
                    class="flex items-center gap-2 text-xl font-bold text-slate-900"
                >
                    <Tags class="size-5 text-[#042163]" />
                    Marcas especializadas
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Administra los logos que aparecen en el inicio del sitio.
                </p>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-[#042163] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#042163]/90"
                @click="openCreate"
            >
                <Plus class="size-4" />
                Nueva marca
            </button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article
                v-for="brand in brands"
                :key="brand.id"
                class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-5"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-14 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                        >
                            <img
                                v-if="brand.logo_url"
                                :src="brand.logo_url"
                                :alt="brand.name"
                                class="max-h-10 max-w-10 object-contain"
                            />
                            <span
                                v-else
                                class="text-sm font-black text-[#042163]"
                            >
                                {{ initials(brand.name) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-bold text-[#042163]">
                                {{ brand.name }}
                            </p>
                            <p class="text-xs text-slate-500">
                                /{{ brand.slug }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        :class="[
                            'relative h-6 w-11 shrink-0 rounded-full transition',
                            brand.is_active ? 'bg-emerald-500' : 'bg-slate-300',
                        ]"
                        :aria-label="
                            brand.is_active
                                ? 'Desactivar marca'
                                : 'Activar marca'
                        "
                        @click="toggleStatus(brand)"
                    >
                        <span
                            :class="[
                                'absolute top-0.5 size-5 rounded-full bg-white shadow transition',
                                brand.is_active ? 'left-5.5' : 'left-0.5',
                            ]"
                        />
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <span
                        :class="[
                            'rounded-full px-2.5 py-1 text-xs font-semibold',
                            brand.is_active
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-slate-100 text-slate-500',
                        ]"
                    >
                        {{ brand.is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                    <span class="text-xs text-slate-500">
                        {{ brand.products_count ?? 0 }} productos
                    </span>
                </div>

                <div
                    class="flex items-center gap-2 border-t border-slate-100 pt-3"
                >
                    <button
                        type="button"
                        class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md border border-slate-200 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#042163]/40 hover:text-[#042163]"
                        @click="openEdit(brand)"
                    >
                        <Pencil class="size-3.5" />
                        Editar
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-1.5 rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#c63427]/40 hover:text-[#c63427]"
                        @click="brandToDelete = brand"
                    >
                        <Trash2 class="size-3.5" />
                        Eliminar
                    </button>
                </div>
            </article>

            <div
                v-if="brands.length === 0"
                class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center sm:col-span-2 xl:col-span-3"
            >
                <p class="font-semibold text-slate-700">
                    Aún no hay marcas registradas
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Agrega la primera marca para mostrarla en el inicio.
                </p>
            </div>
        </div>

        <Dialog :open="dialogOpen" @update:open="(open) => (dialogOpen = open)">
            <DialogContent>
                <form @submit.prevent="submit">
                    <DialogHeader>
                        <DialogTitle>
                            {{ editing ? 'Editar marca' : 'Nueva marca' }}
                        </DialogTitle>
                        <DialogDescription>
                            El logo se muestra en la sección de especialistas
                            del inicio.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="brand-name">Nombre</Label>
                            <Input
                                id="brand-name"
                                v-model="form.name"
                                placeholder="Cummins"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="brand-slug">Slug (opcional)</Label>
                            <Input
                                id="brand-slug"
                                v-model="form.slug"
                                placeholder="se-genera-automaticamente"
                            />
                            <InputError :message="form.errors.slug" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="brand-logo">Logo</Label>
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-16 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50"
                                >
                                    <img
                                        v-if="logoPreview"
                                        :src="logoPreview"
                                        alt="Vista previa"
                                        class="max-h-12 max-w-12 object-contain"
                                    />
                                    <Upload
                                        v-else
                                        class="size-5 text-slate-400"
                                    />
                                </div>
                                <input
                                    id="brand-logo"
                                    type="file"
                                    accept="image/*"
                                    class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-[#042163] file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white"
                                    @change="onLogoChange"
                                />
                            </div>
                            <InputError :message="form.errors.logo" />
                        </div>

                        <label
                            class="flex items-center gap-2 text-sm text-slate-600"
                        >
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="size-4 rounded border-slate-300 text-[#042163] focus:ring-[#042163]"
                            />
                            Marca activa (visible en el sitio)
                        </label>
                    </div>

                    <DialogFooter>
                        <DialogClose
                            class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                        >
                            Cancelar
                        </DialogClose>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex h-9 items-center justify-center rounded-md bg-[#042163] px-4 text-sm font-semibold text-white transition hover:bg-[#042163]/90 disabled:opacity-60"
                        >
                            {{ form.processing ? 'Guardando…' : 'Guardar' }}
                        </button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog
            :open="brandToDelete !== null"
            @update:open="(open) => !open && (brandToDelete = null)"
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Eliminar marca</DialogTitle>
                    <DialogDescription>
                        ¿Eliminar <strong>{{ brandToDelete?.name }}</strong
                        >? Los productos asociados quedarán sin marca.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose
                        class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Cancelar
                    </DialogClose>
                    <button
                        type="button"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#c63427] px-4 text-sm font-semibold text-white hover:bg-[#a82b20]"
                        @click="confirmDelete"
                    >
                        Eliminar
                    </button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
