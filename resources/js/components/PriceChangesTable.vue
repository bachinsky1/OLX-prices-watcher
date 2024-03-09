<template>
    <div>
        <h2 class="text-xl font-bold mb-4">Зміна цін</h2>
        <template v-if="subscriptions.length > 0">
            <table class="min-w-full divide-y divide-gray-200 bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-800 border border-gray-300 dark:border-gray-700">
                <thead class="bg-gray-300 dark:bg-gray-700 dark:text-gray-100 border border-gray-300 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"> ID </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"> Назва товару </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"> Поточна ціна </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"> Email підписника </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"> Email підтверджено </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"> Дата оновлення </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(subscription, index) in subscriptions" :key="subscription.id" :class="{ 'bg-gray-100': index % 2 === 0, 'dark:bg-gray-300': index % 2 !== 0 }" class="border-b dark:border-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ subscription.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a :href="subscription.ad.url" class="text-blue-600 hover:text-blue-800" target="_blank">{{ subscription.ad.price.title || '' }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ subscription.ad.price.value || '' }} {{ subscription.ad.price.currency_symbol || '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ subscription.email.email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ subscription.email.confirmed ? 'Так' : 'Ні' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ subscription.ad.price.updated_at }}</td>
                    </tr>
                </tbody>
            </table>
            <!-- Пагінація -->
            <nav class="flex items-center justify-end mt-4" v-if="pagination.links && pagination.last_page > 1">
                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                        <button :disabled="!pagination.links[0]?.url" @click="fetchSubscriptions(pagination.links[0]?.url)" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"> Previous </button>
                        <div v-for="link in pagination.links.slice(1, pagination.links.length - 1)" :key="link.label">
                            <button @click="fetchSubscriptions(link.url)" :class="{ 'bg-gray-200': link.active }" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ link.label }}
                            </button>
                        </div>
                        <button :disabled="!pagination.links[pagination.links.length - 1]?.url" @click="fetchSubscriptions(pagination.links[pagination.links.length - 1]?.url)" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"> Next </button>
                    </span>
                </div>
            </nav>
        </template>
        <!-- Виведення інформації про відсутність змін цін -->
        <template v-else>
            <p>Немає підписок</p>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const subscriptions = ref([]);
const pagination = ref([]);

const fetchSubscriptions = async (url) => {
    try {
        const response = await axios.get(url);
        subscriptions.value = response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching subscriptions:', error);
    }
};

onMounted(() => {
    fetchSubscriptions('/api/changes?page=1'); 
});
</script>
