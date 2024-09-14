import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AboutView from '@/views/AboutView.vue'
import AIView from '../views/AIView.vue';
import OpenAIView from '../views/OpenAIView.vue';

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      component: AboutView
    },
    {
      path: '/ai',
      name: 'ai',
      component: AIView, // Add the AI route
    },
    {
      path: '/openai',
      name: 'openai',
      component: OpenAIView, // Add the AI route
    },
  ]
})

export default router
