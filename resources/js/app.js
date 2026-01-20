import './bootstrap';

import Alpine from 'alpinejs';
import { initToasts } from './ui/toast';

window.Alpine = Alpine;

Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initToasts();
    });
} else {
    initToasts();
}
