import './bootstrap';
import Alpine from '@alpinejs/csp';
import { registerAlpineComponents } from './alpine-components';
import { initMotion } from './motion';

document.addEventListener('alpine:init', () => {
    registerAlpineComponents(Alpine);
});

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initMotion();

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
