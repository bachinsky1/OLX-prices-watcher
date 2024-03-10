import { defineStore } from 'pinia';
import axios from 'axios';

export const useSubscriptionsStore = defineStore('subscriptions', {
    state: () => ({
        subscriptions: [],
        pagination: {},
    }),
    actions: {
        async fetchSubscriptions(page = 1) {
            try {
                const response = await axios.get(`/api/subscriptions?page=${page}`);
                this.subscriptions = response.data.data;
                this.pagination = response.data;
            } catch (error) {
                console.error('Error fetching subscriptions:', error);
            }
        },
    },
});
