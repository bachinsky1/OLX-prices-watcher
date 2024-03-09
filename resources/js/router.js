import { createRouter, createWebHistory } from 'vue-router'
import SubscriptionsTable from './components/SubscriptionsTable.vue'
import PriceChangesTable from './components/PriceChangesTable.vue'

const routes = [
  { path: '/subscriptions', component: SubscriptionsTable },
  { path: '/price-changes', component: PriceChangesTable },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
