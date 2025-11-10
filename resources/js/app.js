import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

// Inicializar Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Exponer Chart.js globalmente
window.Chart = Chart;
