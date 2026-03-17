import { ref, computed, watch } from 'vue';

export interface CartItem {
    productId: number;
    slug: string;
    nameZh: string;
    nameEn: string | null;
    price: number; // in cents
    quantity: number;
    image: string | null;
    stock: number;
}

const STORAGE_KEY = 'amyshouse-cart';

function loadCart(): CartItem[] {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    } catch {
        return [];
    }
}

const items = ref<CartItem[]>(loadCart());

watch(items, (val) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(val));
}, { deep: true });

export function useCart() {
    const totalItems = computed(() =>
        items.value.reduce((sum, item) => sum + item.quantity, 0),
    );

    const totalPrice = computed(() =>
        items.value.reduce((sum, item) => sum + item.price * item.quantity, 0),
    );

    function addItem(product: Omit<CartItem, 'quantity'>, quantity = 1): void {
        const existing = items.value.find((i) => i.productId === product.productId);

        if (existing) {
            existing.quantity = Math.min(existing.quantity + quantity, product.stock);
        } else {
            items.value.push({ ...product, quantity });
        }
    }

    function updateQuantity(productId: number, quantity: number): void {
        const item = items.value.find((i) => i.productId === productId);
        if (item) {
            if (quantity <= 0) {
                removeItem(productId);
            } else {
                item.quantity = Math.min(quantity, item.stock);
            }
        }
    }

    function removeItem(productId: number): void {
        items.value = items.value.filter((i) => i.productId !== productId);
    }

    function clearCart(): void {
        items.value = [];
    }

    return {
        items,
        totalItems,
        totalPrice,
        addItem,
        updateQuantity,
        removeItem,
        clearCart,
    };
}
