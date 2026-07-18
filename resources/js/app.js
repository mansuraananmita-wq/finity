import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('hero-video');
    if (video) {
        const isMobile = window.innerWidth < 768;
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!prefersReducedMotion && window.innerWidth >= 480) {
            video.preload = isMobile ? 'metadata' : 'auto';
        }
    }
});

Alpine.data('cartActions', () => ({
    async addToCart(productId, quantity = 1) {
        const res = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ product_id: productId, quantity }),
        });
        const data = await res.json();
        if (res.ok && data.cart) {
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: data.cart }));
        }
        return data;
    },
    async toggleWishlist(productId) {
        const res = await fetch(`/wishlist/${productId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        return res.json();
    },
}));

Alpine.start();
