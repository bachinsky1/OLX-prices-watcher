<template>
    <div v-show="isVisible" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white rounded-lg p-4 max-w-md w-full">
            <!-- Хедер з кнопкою закриття -->
            <header class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Створити нову підписку</h2>
                <button @click="cancel" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </header>
            <!-- Форма -->
            <form @submit.prevent="submitForm">
                <div class="mt-4">
                    <label for="ad_url" class="block">URL оголошення</label>
                    <input type="text" v-model="form.ad_url" class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div class="mt-4">
                    <label for="email" class="block">Email</label>
                    <input type="email" v-model="form.email" class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"> Підписатися </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';
import axios from 'axios';
import { useSubscriptionsStore } from '../store';

const isVisible = ref(true);
const emit = defineEmits(['close']);

const form = ref({
    ad_url: '',
    email: ''
});

const store = useSubscriptionsStore(); // Додано стор до залежностей

const submitForm = async () => {
    try {
        const response = await axios.post('/api/subscribe', form.value);
        
        // Оновлюємо підписки з повідомленням
        store.setSubscriptions(response.data.message); 

        // Закриваємо форму після успішної підписки
        cancel();
        alert(response.data.message)
    } catch (error) {
        console.error('Error:', error);
        alert('Помилка при виконанні запиту');
    }
};

const cancel = () => {
    isVisible.value = false;
    emit('close');
};

const resetForm = () => {
    form.value.ad_url = '';
    form.value.email = '';
};

</script>
