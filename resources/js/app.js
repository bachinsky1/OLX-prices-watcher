// @ts-nocheck
import './bootstrap'
import { createApp } from 'vue';
import App from './App.vue'; // Головний компонент App
import router from './router';
import { createPinia } from 'pinia'; // Імпорт Pinia

// Створення і монтування кореневого екземпляра Vue
const app = createApp(App);

// Додавання використання Pinia до додатка
const pinia = createPinia();
app.use(pinia);

// Додавання роутера до додатка
app.use(router);

// Монтування додатка
app.mount('#app');
