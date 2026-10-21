import {
    computed,
    getCurrentInstance,
    onMounted,
    reactive,
    ref,
    watch,
} from 'vue';
import type { Product } from '@/types';

export type CartItem = {
    id: number;
    name: string;
    slug: string;
    sku: string;
    price: number;
    image_url: string | null;
    brand: string | null;
    quantity: number;
};

const STORAGE_KEY = 'transdiesel:cart';

const state = reactive<{ items: CartItem[]; isOpen: boolean }>({
    items: [],
    isOpen: false,
});

/**
 * Cart data lives in localStorage, so it must only be read after mounting to
 * keep the server-rendered markup and the hydrated markup identical.
 */
const ready = ref(false);

let hydrated = false;
let hydrationScheduled = false;

function persist(): void {
    if (typeof window === 'undefined') {
        return;
    }

    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(state.items));
}

function hydrate(): void {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        const raw = window.localStorage.getItem(STORAGE_KEY);

        if (raw) {
            const parsed: unknown = JSON.parse(raw);

            if (Array.isArray(parsed)) {
                state.items = parsed as CartItem[];
            }
        }
    } catch {
        state.items = [];
    }
}

watch(
    () => state.items,
    () => {
        if (hydrated) {
            persist();
        }
    },
    { deep: true },
);

const totalItems = computed(() =>
    state.items.reduce((total, item) => total + item.quantity, 0),
);

const subtotal = computed(() =>
    state.items.reduce((total, item) => total + item.price * item.quantity, 0),
);

/**
 * Build the WhatsApp checkout link with the serialized cart contents.
 */
function whatsappCheckoutUrl(): string {
    const lines: string[] = [
        'Hola TRANSDIESEL, quiero cotizar los siguientes productos:',
    ];

    state.items.forEach((item) => {
        lines.push(
            `• ${item.quantity} x ${item.name} (SKU ${item.sku}) — ${formatLine(item)}`,
        );
    });

    lines.push('', `Total estimado: ${formatLineTotal()}`);

    return `https://wa.me/523322544431?text=${encodeURIComponent(lines.join('\n'))}`;
}

function formatLine(item: CartItem): string {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(item.price * item.quantity);
}

function formatLineTotal(): string {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(subtotal.value);
}

export function useCart() {
    if (!hydrationScheduled) {
        hydrationScheduled = true;

        if (getCurrentInstance()) {
            onMounted(() => {
                hydrate();
                hydrated = true;
                ready.value = true;
            });
        } else {
            hydrate();
            hydrated = true;
            ready.value = true;
        }
    }

    function addItem(product: Product, quantity = 1): void {
        const existing = state.items.find((item) => item.id === product.id);

        if (existing) {
            existing.quantity += quantity;
        } else {
            state.items.push({
                id: product.id,
                name: product.name,
                slug: product.slug,
                sku: product.sku,
                price: product.price,
                image_url: product.image_url,
                brand: product.brand?.name ?? null,
                quantity,
            });
        }

        state.isOpen = true;
    }

    function removeItem(id: number): void {
        state.items = state.items.filter((item) => item.id !== id);
    }

    function setQuantity(id: number, quantity: number): void {
        const item = state.items.find((entry) => entry.id === id);

        if (!item) {
            return;
        }

        item.quantity = Math.max(1, Math.min(99, Math.trunc(quantity) || 1));
    }

    function clear(): void {
        state.items = [];
    }

    return {
        items: computed(() => state.items),
        isOpen: computed(() => state.isOpen),
        ready,
        totalItems,
        subtotal,
        addItem,
        removeItem,
        setQuantity,
        clear,
        open: () => (state.isOpen = true),
        close: () => (state.isOpen = false),
        toggle: () => (state.isOpen = !state.isOpen),
        whatsappCheckoutUrl,
    };
}
