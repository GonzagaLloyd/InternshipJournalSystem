<script setup>
import { computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    message: {
        type: String,
        default: 'Preserving to Ledger...'
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition 
            enter-active-class="duration-1000 ease-out" 
            enter-from-class="opacity-0" 
            enter-to-class="opacity-100" 
            leave-active-class="duration-700 ease-in" 
            leave-from-class="opacity-100" 
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[99999] flex flex-col items-center justify-center overflow-hidden bg-[#1B1B1B]">
                <!-- Minimal Atmosphere -->
                <div class="absolute inset-0">
                    <!-- Soft Center Glow -->
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_#2D2D2D_0%,_#1B1B1B_70%)] opacity-60"></div>
                    <!-- Very subtle noise for texture -->
                    <div class="absolute inset-0 opacity-[0.05] bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]"></div>
                </div>

                <!-- Loader Content -->
                <div class="relative flex flex-col items-center">
                    
                    <!-- Central Element -->
                    <div class="relative">
                        <!-- Single Elegant Ring -->
                        <div class="absolute inset-[-30px] border border-[#C9B79C]/10 rounded-full animate-[spin_20s_linear_infinite]"></div>
                        
                        <!-- Logo with subtle float -->
                        <div class="relative z-10 animate-gentle-float">
                            <img src="@images/JournalSystemLogo.png" alt="Journal Logo" class="w-24 h-24 object-contain filter drop-shadow-[0_0_15px_rgba(201,183,156,0.1)] brightness-[0.9] sepia-[0.3]" />
                        </div>
                    </div>

                    <!-- Simple Clean Text -->
                    <div class="mt-12 overflow-hidden px-6">
                        <p class="text-[#C9B79C]/40 font-cinzel text-[9px] sm:text-[10px] tracking-[0.4em] sm:tracking-[0.8em] uppercase animate-pulse-slow text-center">
                            {{ message }}
                        </p>
                    </div>

                    <!-- Progress Accent (Thin bottom line) -->
                    <div class="mt-4 w-32 h-[1px] bg-gradient-to-r from-transparent via-[#8C6A4A]/30 to-transparent"></div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes gentle-float {
    0%, 100% { transform: translateY(0); opacity: 0.8; }
    50% { transform: translateY(-8px); opacity: 1; }
}

.animate-gentle-float {
    animation: gentle-float 4s ease-in-out infinite;
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; letter-spacing: 0.8em; }
    50% { opacity: 0.6; letter-spacing: 0.85em; }
}

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}
</style>
