import Alpine from 'alpinejs';
window.Alpine = Alpine;

import note from './alpine/note';
Alpine.data('note', () => note);

Alpine.start();