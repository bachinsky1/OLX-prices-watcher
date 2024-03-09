import { defineStore } from 'pinia'

// Оголошення Pinia-стору для управління підписками
export const useSubscriptionsStore = defineStore('subscriptions', {
    // Визначення початкового стану стору
    state: () => ({
        subscriptions: [] // Початковий стан містить порожній масив підписок
    }),
    // Визначення дій для зміни стану стору
    actions: {
        // Дія для оновлення списку підписок
        setSubscriptions (subscriptions) {
            this.subscriptions = subscriptions // Присвоюємо новий список підписок до властивості 'subscriptions'
        }
    }
})
