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
    }, 30000); // 30 seconds cycle
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#020617] overflow-hidden relative font-sans">
        <!-- High Quality Background Carousel -->
        <div class="absolute inset-0 z-0 overflow-hidden bg-[#020617]">
            <Transition name="fade" mode="out-in">
                <img 
                    :key="currentBg"
                    :src="backgrounds[currentBg]" 
                    class="absolute inset-0 w-full h-full object-cover opacity-80 scale-105"
                    alt="Background"
                />
            </Transition>
            <div class="absolute inset-0 bg-slate-950/30 backdrop-blur-[1px]"></div>
        </div>

        <div class="relative z-10 mb-8 drop-shadow-[0_20px_20px_rgba(0,0,0,0.4)] transition-all duration-700 hover:scale-105">
            <Link href="/">
                <ApplicationLogo class="h-64 fill-current text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.3)] transition-all hover:drop-shadow-[0_0_25_rgba(255,255,255,0.5)]" />
            </Link>
        </div>

        <div
            class="relative z-10 w-full sm:max-w-md mt-4 px-10 py-12 bg-white/5 backdrop-blur-3xl overflow-hidden sm:rounded-3xl border border-white/10 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] transition-all duration-500 hover:border-white/20"
        >
            <slot />
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
