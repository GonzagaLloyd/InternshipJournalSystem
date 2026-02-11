<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import Toast from '@/Components/UI/Toast.vue';

const backgrounds = [
    'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2070&auto=format&fit=crop', // Ancient Library
    'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=2070&auto=format&fit=crop', // Books
    'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=2070&auto=format&fit=crop', // Writing
    'https://images.unsplash.com/photo-1461360228754-6e81c478c882?q=80&w=2070&auto=format&fit=crop', // Clock
    'https://images.unsplash.com/photo-1585670210693-e7fdd16b142e?q=80&w=2070&auto=format&fit=crop'  // Parchment
];

const currentBg = ref(0);
let intervalId = null;

onMounted(() => {
    intervalId = setInterval(() => {
        currentBg.value = (currentBg.value + 1) % backgrounds.length;
    }, 8000); // 15 seconds cycle
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<template>
    <div class="relative min-h-screen flex flex-col items-center justify-center p-6 overflow-hidden bg-[#1B1B1B] font-serif">
        <!-- Background Carousel -->
        <div class="absolute inset-0 z-0">
            <Transition name="fade" mode="out-in">
                <img 
                    :key="currentBg"
                    :src="backgrounds[currentBg]" 
                    class="absolute inset-0 w-full h-full object-cover scale-105 transition-all duration-[3000ms] brightness-[0.2] sepia-[0.3]"
                    alt="Background"
                />
            </Transition>
            <!-- Overlay and Blur -->
            <div class="absolute inset-0 bg-[#1B1B1B]/60 backdrop-blur-[2px]"></div>
        </div>

        <!-- Content Container (Pulled up for better balance) -->
        <div class="relative z-10 flex flex-col items-center w-full max-w-lg -mt-[8vh]">
            <div class="mb-2 drop-shadow-[0_20px_30px_rgba(0,0,0,0.5)] transition-all duration-700 hover:scale-105">
                <Link href="/">
                    <ApplicationLogo class="h-64 w-auto fill-current text-[#C9B79C] drop-shadow-[0_0_20px_rgba(201,183,156,0.4)]" />
                </Link>
            </div>

            <div
                class="w-full bg-[#2D2D2D]/80 backdrop-blur-[45px] overflow-hidden rounded-[2.5rem] border border-white/5 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.7)] transition-all duration-500 hover:bg-[#2D2D2D]/90 hover:border-white/10 px-8 py-10 sm:px-12 sm:py-14"
            >
                <slot />
            </div>
        </div>

        <Toast />
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 3s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

