<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ImagePlus, Plus, Save, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store, update } from '@/routes/admin/products';
import type {
    AvailabilityOption,
    AvailabilityValue,
    Brand,
    Category,
    Product,
} from '@/types';

const props = defineProps<{
    product?: Product;
    categories: Category[];
    brands: Brand[];
    availabilityOptions: AvailabilityOption[];
}>();

type SpecificationRow = { key: string; value: string };

const specificationRows = ref<SpecificationRow[]>(
    Object.entries(props.product?.specifications ?? {}).map(([key, value]) => ({
        key,
        value,
    })),
);

const imagePreview = ref<string | null>(props.product?.image_url ?? null);

const form = useForm({
    name: props.product?.name ?? '',
    sku: props.product?.sku ?? '',
    slug: props.product?.slug ?? '',
    category_id: props.product?.category_id ?? ('' as number | ''),
    brand_id: props.product?.brand_id ?? ('' as number | ''),
    price: props.product ? String(props.product.price) : '',
    availability: (props.product?.availability ??
        'in_stock') as AvailabilityValue,
    description: props.product?.description ?? '',
    specifications: [] as SpecificationRow[],
    image: null as File | null,
});

function onFileChange(event: Event): void {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    form.image = file;

    if (file) {
        imagePreview.value = URL.createObjectURL(file);
    }
}

function addSpecification(): void {
    specificationRows.value.push({ key: '', value: '' });
}

function removeSpecification(index: number): void {
    specificationRows.value.splice(index, 1);
}

function submit(): void {
    form.specifications = specificationRows.value.filter(
        (row) => row.key.trim() !== '' && row.value.trim() !== '',
    );

    const options = {
        forceFormData: true,
        preserveScroll: true,
    };

    if (props.product) {
        form.patch(update(props.product.id).url, options);
    } else {
        form.post(store().url, options);
    }
}
</script>

<template>
    <form class="grid gap-6 lg:grid-cols-[1fr_360px]" @submit.prevent="submit">
        <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6">
            <div class="grid gap-2">
                <Label for="name">Nombre del producto</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    placeholder="Bomba de inyección Bosch VP44"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="sku">SKU / Código de parte</Label>
                    <Input
                        id="sku"
                        v-model="form.sku"
                        placeholder="TD-BOM-1001"
                    />
                    <InputError :message="form.errors.sku" />
                </div>

                <div class="grid gap-2">
                    <Label for="slug">Slug (opcional)</Label>
                    <Input
                        id="slug"
                        v-model="form.slug"
                        placeholder="se-genera-automaticamente"
                    />
                    <InputError :message="form.errors.slug" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="category_id">Categoría</Label>
                    <select
                        id="category_id"
                        v-model="form.category_id"
                        class="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                    >
                        <option value="" disabled>
                            Selecciona una categoría
                        </option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="brand_id">Marca</Label>
                    <select
                        id="brand_id"
                        v-model="form.brand_id"
                        class="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                    >
                        <option value="">Sin marca</option>
                        <option
                            v-for="brand in brands"
                            :key="brand.id"
                            :value="brand.id"
                        >
                            {{ brand.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.brand_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="price">Precio (MXN)</Label>
                    <Input
                        id="price"
                        v-model="form.price"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="18900.00"
                    />
                    <InputError :message="form.errors.price" />
                </div>

                <div class="grid gap-2">
                    <Label for="availability">Disponibilidad</Label>
                    <select
                        id="availability"
                        v-model="form.availability"
                        class="h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                    >
                        <option
                            v-for="option in availabilityOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.availability" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="description">Descripción</Label>
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#042163] focus:ring-2 focus:ring-[#042163]/20"
                    placeholder="Descripción técnica del producto"
                />
                <InputError :message="form.errors.description" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <Label>Ficha técnica</Label>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-[#042163] hover:underline"
                        @click="addSpecification"
                    >
                        <Plus class="size-3.5" />
                        Agregar campo
                    </button>
                </div>

                <div class="mt-2 space-y-2">
                    <div
                        v-for="(row, index) in specificationRows"
                        :key="index"
                        class="flex items-center gap-2"
                    >
                        <Input
                            v-model="row.key"
                            placeholder="Presión de trabajo"
                            class="flex-1"
                        />
                        <Input
                            v-model="row.value"
                            placeholder="1,800 bar"
                            class="flex-1"
                        />
                        <button
                            type="button"
                            class="flex size-9 shrink-0 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition hover:border-[#c63427]/40 hover:text-[#c63427]"
                            aria-label="Eliminar campo"
                            @click="removeSpecification(index)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>

                    <p
                        v-if="specificationRows.length === 0"
                        class="rounded-md border border-dashed border-slate-200 px-3 py-4 text-center text-xs text-slate-500"
                    >
                        Sin ficha técnica. Agrega campos como presión,
                        compatibilidad o garantía.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <Label for="image">Imagen del producto</Label>

                <div
                    class="mt-2 flex aspect-square items-center justify-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-slate-50"
                >
                    <img
                        v-if="imagePreview"
                        :src="imagePreview"
                        alt="Vista previa"
                        class="size-full object-cover"
                    />
                    <div
                        v-else
                        class="flex flex-col items-center gap-2 text-slate-400"
                    >
                        <ImagePlus class="size-8" />
                        <span class="text-xs">Sin imagen</span>
                    </div>
                </div>

                <input
                    id="image"
                    type="file"
                    accept="image/*"
                    class="mt-3 block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-[#042163] file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-[#042163]/90"
                    @change="onFileChange"
                />
                <InputError :message="form.errors.image" />
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#042163] text-sm font-bold text-white transition hover:bg-[#042163]/90 disabled:opacity-60"
            >
                <Save class="size-4" />
                {{ form.processing ? 'Guardando…' : 'Guardar producto' }}
            </button>
        </div>
    </form>
</template>
