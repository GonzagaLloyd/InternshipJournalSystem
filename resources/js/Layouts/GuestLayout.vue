<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const backgrounds = [
    'https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=2070&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1519750783826-e2420f4d687f?q=80&w=1974&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1964&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1635776062127-d379bfcbb9c8?q=80&w=2032&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1533134486753-c833f0ed4866?q=80&w=2070&auto=format&fit=crop'
];

const currentBg = ref(0);
let intervalId = null;

onMounted(() => {
    intervalId = setInterval(() => {
        currentBg.value = (currentBg.value + 1) % backgrounds.length;
    }, 5000); // 15 seconds cycle
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<template>
    <div class="relative min-h-screen flex flex-col items-center justify-center p-6 overflow-hidden bg-slate-950">
        <!-- Background Carousel -->
        <div class="absolute inset-0 z-0">
            <Transition name="fade" mode="out-in">
                <img 
                    :key="currentBg"
                    :src="backgrounds[currentBg]" 
                    class="absolute inset-0 w-full h-full object-cover scale-105 transition-all duration-[3000ms] brightness-[0.4]"
                    alt="Background"
                />
            </Transition>
            <!-- Overlay and Blur -->
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[2px]"></div>
        </div>

        <!-- Content Container (Pulled up for better balance) -->
        <div class="relative z-10 flex flex-col items-center w-full max-w-lg -mt-[8vh]">
            <div class="mb-2 drop-shadow-[0_20px_30px_rgba(0,0,0,0.5)] transition-all duration-700 hover:scale-105">
                <Link href="/">
                    <ApplicationLogo class="h-64 w-auto fill-current text-white drop-shadow-[0_0_20px_rgba(255,255,255,0.4)]" />
                </Link>
            </div>

            <div
                class="w-full bg-white/[0.08] backdrop-blur-[45px] overflow-hidden rounded-[2.5rem] border border-white/20 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.7)] transition-all duration-500 hover:bg-white/[0.1] hover:border-white/30 px-8 py-10 sm:px-12 sm:py-14"
            >
                <slot />
            </div>
        </div>
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

