import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-img-fallback]').forEach((img) => {
        img.addEventListener('error', function onError() {
            this.removeEventListener('error', onError);
            this.classList.add('hidden');
            const fallback = this.nextElementSibling;
            if (fallback && fallback.hasAttribute('data-img-placeholder')) {
                fallback.classList.remove('hidden');
            }
        });
    });
});
