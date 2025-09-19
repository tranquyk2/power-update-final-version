import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

export const isLoading = ref(false);

router.on('start', () => {
  isLoading.value = true;
});

router.on('finish', () => {
  isLoading.value = false;
});