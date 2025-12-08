import { createRouter, createWebHistory } from 'vue-router';
import Home from './pages/Home.vue';
import ItineraryDetail from './pages/ItineraryDetail.vue';
import TourPackages from './pages/TourPackages.vue';
import TourPackageDetail from './pages/TourPackageDetail.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/itineraries/:slug',
    name: 'itinerary-detail',
    component: ItineraryDetail,
  },
  {
    path: '/tour-packages',
    name: 'tour-packages',
    component: TourPackages,
  },
  {
    path: '/tour-packages/:slug',
    name: 'tour-package-detail',
    component: TourPackageDetail,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  },
});

export default router;

