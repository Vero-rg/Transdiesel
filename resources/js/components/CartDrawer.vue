<script setup lang="ts">
import {
    ImageOff,
    MessageCircle,
    Minus,
    Plus,
    ShoppingBag,
    Trash2,
} from '@lucide/vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { useCart } from '@/composables/useCart';
import { formatCurrency } from '@/lib/format';

const {
    items,
    isOpen,
    subtotal,
    totalItems,
    removeItem,
    setQuantity,
    clear,
    close,
    whatsappCheckoutUrl,
} = useCart();

function handleOpenChange(value: boolean): void {
    if (!value) {
        close();
    }
}
</script>

<template>
    <Sheet :open="isOpen" @update:open="handleOpenChange">
        <SheetContent
            side="right"
            class="flex w-full flex-col gap-0 bg-white p-0 sm:max-w-md"
        >
            <SheetHeader
                class="flex-row items-center justify-between border-b border-slate-200 px-6 py-5"
            >
                <div>
                    <SheetTitle
                        class="flex items-center gap-2 text-lg font-bold text-[#042163]"
                    >
                        <ShoppingBag class="size-5" />
                        Tu carrito
                    </SheetTitle>
                    <SheetDescription class="text-sm text-slate-500">
                        {{ totalItems }}
                        {{ totalItems === 1 ? 'producto' : 'productos' }} para
                        cotizar
                    </SheetDescription>
                </div>
            </SheetHeader>

            <div class="flex-1 overflow-y-auto px-6 py-4">
                <div
                    v-if="items.length === 0"
                    class="flex h-full flex-col items-center justify-center gap-3 py-16 text-center"
                >
                    <div
                        class="flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-400"
                    >
                        <ShoppingBag class="size-7" />
                    </div>
                    <p class="font-semibold text-slate-700">
                        Tu carrito está vacío
                    </p>
                    <p class="max-w-56 text-sm text-slate-500">
                        Agrega bombas o inyectores para solicitar tu cotización
                        por WhatsApp.
                    </p>
                </div>

                <ul v-else class="flex flex-col gap-4">
                    <li
                        v-for="item in items"
                        :key="item.id"
                        class="flex gap-3 rounded-xl border border-slate-200 p-3"
                    >
                        <div
                            class="size-16 shrink-0 overflow-hidden rounded-lg bg-slate-100"
                        >
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                :alt="item.name"
                                class="size-full object-cover"
                            />
                            <div
                                v-else
                                class="flex size-full items-center justify-center text-slate-400"
                            >
                                <ImageOff class="size-5" />
                            </div>
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col gap-1">
                            <p
                                class="line-clamp-2 text-sm leading-snug font-semibold text-[#042163]"
                            >
                                {{ item.name }}
                            </p>
                            <p
                                class="font-mono text-[11px] tracking-wide text-slate-500 uppercase"
                            >
                                {{ item.sku }}
                            </p>

                            <div
                                class="mt-1 flex items-center justify-between gap-2"
                            >
                                <div
                                    class="flex items-center rounded-lg border border-slate-200"
                                >
                                    <button
                                        type="button"
                                        class="flex size-7 items-center justify-center text-slate-500 transition hover:text-[#c63427]"
                                        aria-label="Quitar una unidad"
                                        @click="
                                            setQuantity(
                                                item.id,
                                                item.quantity - 1,
                                            )
                                        "
                                    >
                                        <Minus class="size-3.5" />
                                    </button>
                                    <span
                                        class="w-7 text-center text-sm font-semibold text-slate-700"
                                    >
                                        {{ item.quantity }}
                                    </span>
                                    <button
                                        type="button"
                                        class="flex size-7 items-center justify-center text-slate-500 transition hover:text-[#042163]"
                                        aria-label="Agregar una unidad"
                                        @click="
                                            setQuantity(
                                                item.id,
                                                item.quantity + 1,
                                            )
                                        "
                                    >
                                        <Plus class="size-3.5" />
                                    </button>
                                </div>

                                <p class="text-sm font-bold text-[#042163]">
                                    {{
                                        formatCurrency(
                                            item.price * item.quantity,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="self-start text-slate-400 transition hover:text-[#c63427]"
                            aria-label="Eliminar del carrito"
                            @click="removeItem(item.id)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </li>
                </ul>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 px-6 py-5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Subtotal</span>
                    <span class="font-semibold text-[#042163]">
                        {{ formatCurrency(subtotal) }}
                    </span>
                </div>
                <div
                    class="mt-1 flex items-center justify-between text-base font-bold"
                >
                    <span class="text-[#042163]">Total estimado</span>
                    <span class="text-[#042163]">
                        {{ formatCurrency(subtotal) }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-slate-500">
                    Precios en MXN. La cotización final se confirma por
                    WhatsApp.
                </p>

                <a
                    :href="whatsappCheckoutUrl()"
                    target="_blank"
                    rel="noopener noreferrer"
                    :class="[
                        'mt-4 flex h-11 w-full items-center justify-center gap-2 rounded-lg text-sm font-bold text-white transition',
                        items.length === 0
                            ? 'pointer-events-none bg-slate-300'
                            : 'bg-[#25D366] hover:bg-[#1ebe5b]',
                    ]"
                    :aria-disabled="items.length === 0"
                >
                    <MessageCircle class="size-4" />
                    Cotizar por WhatsApp
                </a>

                <button
                    v-if="items.length > 0"
                    type="button"
                    class="mt-2 w-full text-xs font-medium text-slate-500 underline-offset-4 hover:text-[#c63427] hover:underline"
                    @click="clear"
                >
                    Vaciar carrito
                </button>
            </div>
        </SheetContent>
    </Sheet>
</template>
