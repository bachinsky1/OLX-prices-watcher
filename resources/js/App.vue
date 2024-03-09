<template>
    <div id="app" class="flex flex-col min-h-screen">
        <header class="bg-gray-100 dark:bg-gray-800 dark:text-white p-4">Хедер</header>
        <div class="flex-1 flex">
            <aside class="w-64 bg-gray-200 dark:bg-gray-900 dark:text-white">
                <ul>
                    <li class="p-3 w-full">
                        <button @click="openSubscriptionModal" class="btn btn-primary flex flex-1 w-full">Створити нову підписку</button>
                    </li>
                    <li class="p-3 w-full" :class="{ 'bg-gray-400 dark:bg-gray-700': $route.path === '/subscriptions' }">
                        <router-link to="/subscriptions" class="btn btn-primary flex flex-1 w-full">Підписки</router-link>
                    </li>
                    <li class="p-3 w-full" :class="{ 'bg-gray-400 dark:bg-gray-700': $route.path === '/price-changes' }">
                        <router-link to="/price-changes" class="btn btn-primary flex flex-1 w-full">Зміни цін</router-link>
                    </li>
                </ul>
            </aside>
            <main class="flex-1 bg-white dark:bg-gray-300 bg-gray-100 w-full p-8">
                <div class="h-full">
                    <router-view></router-view>
                </div>
            </main>
        </div>
        <footer class="bg-gray-100 dark:bg-gray-800 dark:text-white p-4 text-center">Футер</footer>
        <SubscriptionForm v-if="showSubscriptionModal" @close="closeSubscriptionModal" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import SubscriptionForm from './components/SubscriptionForm.vue';

const props = defineProps({
    showModal: Boolean
});
const showSubscriptionModal = ref(false);

const openSubscriptionModal = () => {
    showSubscriptionModal.value = true;
};

const closeSubscriptionModal = () => {
    showSubscriptionModal.value = false;
};
</script>
