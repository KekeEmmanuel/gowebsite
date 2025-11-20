import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './style.css';

document.addEventListener('DOMContentLoaded', () => {
  createApp(App).use(router).mount('#marketing-app');
});
