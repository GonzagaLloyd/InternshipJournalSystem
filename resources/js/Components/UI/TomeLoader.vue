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
            <div v-if="show" class="fixed inset-0 z-[99999] flex flex-col items-center justify-center overflow-hidden bg-[#0c0a09]">
                <!-- Minimal Atmosphere -->
                <div class="absolute inset-0">
                    <!-- Soft Center Glow -->
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_#1a1412_0%,_#0c0a09_70%)] opacity-60"></div>
                    <!-- Very subtle noise for texture -->
                    <div class="absolute inset-0 opacity-[0.02] bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]"></div>
                </div>

                <!-- Loader Content -->
                <div class="relative flex flex-col items-center">
                    
                    <!-- Central Element -->
                    <div class="relative">
                        <!-- Single Elegant Ring -->
                        <div class="absolute inset-[-30px] border border-[#f4e4bc]/10 rounded-full animate-[spin_20s_linear_infinite]"></div>
                        
                        <!-- Logo with subtle float -->
                        <div class="relative z-10 animate-gentle-float">
                            <img src="@images/JournalSystemLogo.png" alt="Journal Logo" class="w-24 h-24 object-contain filter drop-shadow-[0_0_15px_rgba(244,228,188,0.1)]" />
                        </div>
                    </div>

                    <!-- Simple Clean Text -->
                    <div class="mt-12 overflow-hidden">
                        <p class="text-[#f4e4bc]/40 font-cinzel text-[9px] tracking-[0.8em] uppercase animate-pulse-slow">
                            {{ message }}
                        </p>
                    </div>

                    <!-- Progress Accent (Thin bottom line) -->
                    <div class="mt-4 w-32 h-[1px] bg-gradient-to-r from-transparent via-[#8b2635]/30 to-transparent"></div>
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
