<script setup>
import { ref } from 'vue';

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Are you sure?'
    },
    message: {
        type: String,
        default: 'This action cannot be undone.'
    },
    confirmText: {
        type: String,
        default: 'Confirm'
    },
    cancelText: {
        type: String,
        default: 'Cancel'
    },
    type: {
        type: String,
        default: 'danger' // danger, warning, info
    }
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <Teleport to="body">
        <Transition 
            enter-active-class="transition duration-700 ease-[cubic-bezier(0.2,0,0,1)]" 
            enter-from-class="opacity-0 translate-y-8 scale-[0.98] blur-sm" 
            leave-active-class="transition duration-400 ease-in" 
            leave-to-class="opacity-0 scale-95 blur-sm"
        >
            <div v-if="show" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-[#0a0908]/95 backdrop-blur-md" @click="$emit('close')"></div>
                
                <!-- Content -->
                <div class="relative w-full max-w-md bg-[#2D2D2D] border border-white/5 rounded-[2.5rem] shadow-[0_40px_120px_-20px_rgba(0,0,0,0.8)] overflow-hidden">
                    <!-- Subtle Texture -->
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>
                    
                    <!-- Ambient Glow -->
                    <div v-if="type === 'danger'" class="absolute -top-32 -right-32 w-64 h-64 bg-red-500/10 blur-[100px] pointer-events-none"></div>
                    <div v-else class="absolute -top-32 -right-32 w-64 h-64 bg-[#8C6A4A]/10 blur-[100px] pointer-events-none"></div>

                    <div class="relative z-10 p-8 md:p-10 text-center">
                        <!-- Icon/Visual -->
                        <div class="mb-6 flex justify-center">
                            <div 
                                :class="[
                                    type === 'danger' ? 'border-red-500/20 bg-red-500/5 text-red-500/80' : 'border-[#8C6A4A]/20 bg-[#8C6A4A]/5 text-[#8C6A4A]/80',
                                    'h-16 w-16 rounded-full border flex items-center justify-center shadow-inner'
                                ]"
                            >
                                <svg v-if="type === 'danger'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <svg v-else class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Text Header -->
                        <h2 class="text-2xl md:text-3xl font-cinzel font-bold text-[#C9B79C] mb-4 tracking-wider uppercase">
                            {{ title }}
                        </h2>
                        
                        <p class="text-sm md:text-base text-[#C9B79C]/60 font-serif leading-relaxed mb-10 px-4">
                            {{ message }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button 
                                @click="$emit('confirm')"
                                :class="[
                                    type === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-[#8C6A4A] hover:bg-[#a07e5d]',
                                    'flex-1 relative overflow-hidden group/btn py-4 rounded-xl transition-all active:scale-[0.98]'
                                ]"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:animate-[shimmer_1.5s_infinite]"></div>
                                <span class="relative z-10 text-[#1B1B1B] font-black text-[10px] uppercase tracking-[0.3em] font-serif">
                                    {{ confirmText }}
                                </span>
                            </button>
                            <button 
                                @click="$emit('close')"
                                class="flex-1 border border-white/10 text-[#8C6A4A]/60 py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.3em] hover:bg-white/5 hover:text-[#C9B79C] transition-all font-serif"
                            >
                                {{ cancelText }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes shimmer {
    100% { transform: translateX(100%); }
}
</style>
