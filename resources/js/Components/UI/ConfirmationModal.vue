<script setup>
import { ref } from 'vue';

const props = defineProps({
    show: Boolean,
    processing: {
        type: Boolean,
        default: false
    },
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
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-400 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <!-- Backdrop with high-end blur -->
                <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="$emit('close')"></div>
                
                <!-- Modal content -->
                <div class="relative w-full max-w-lg bg-[#141414] border border-white/[0.08] rounded-[2rem] shadow-[0_40px_100px_rgba(0,0,0,0.9)] overflow-hidden animate-modal-pop">
                    <!-- Subtle ambient glow -->
                    <div :class="[
                        'absolute -top-24 left-1/2 -translate-x-1/2 w-64 h-64 blur-[100px] opacity-20 pointer-events-none',
                        type === 'danger' ? 'bg-red-500' : 'bg-[#A68B6A]'
                    ]"></div>

                    <div class="relative px-8 py-12 flex flex-col items-center text-center">
                        <!-- Icon Circle -->
                        <div :class="[
                            'w-20 h-20 rounded-full flex items-center justify-center mb-8 border-2 shadow-[0_0_30px_rgba(0,0,0,0.5)]',
                            type === 'danger' ? 'bg-red-500/10 border-red-500/30 text-red-500' : 'bg-[#A68B6A]/10 border-[#A68B6A]/30 text-[#A68B6A]'
                        ]">
                            <svg v-if="type === 'danger'" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <svg v-else class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <!-- Content -->
                        <h2 class="text-3xl font-cinzel font-bold text-[#E3D5C1] mb-6 tracking-[0.1em]">
                            {{ title }}
                        </h2>
                        
                        <p class="text-[#8C6A4A] text-base font-serif leading-relaxed max-w-sm mb-12 opacity-80">
                            {{ message }}
                        </p>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-4 w-full">
                            <button 
                                @click="$emit('confirm')" 
                                :disabled="processing"
                                :class="[
                                    'flex-1 order-1 sm:order-2 py-4 px-8 rounded-full text-[11px] font-cinzel font-bold uppercase tracking-[0.2em] transition-all duration-300 shadow-xl flex items-center justify-center gap-3',
                                    processing ? 'opacity-50 scale-95' : '',
                                    type === 'danger' 
                                        ? 'bg-red-600 hover:bg-red-500 text-white shadow-red-900/20' 
                                        : 'bg-[#A68B6A] hover:bg-[#C9B79C] text-[#1B1B1B] shadow-black/40'
                                ]"
                            >
                                <svg v-if="processing" class="animate-spin h-3 w-3" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ processing ? 'Processing...' : confirmText }}</span>
                            </button>
                            
                            <button 
                                @click="$emit('close')" 
                                :disabled="processing"
                                class="flex-1 order-2 sm:order-1 py-4 px-8 rounded-full text-[11px] font-cinzel font-bold uppercase tracking-[0.2em] text-[#8C6A4A]/60 hover:text-[#C9B79C] hover:bg-white/[0.03] border border-white/5 transition-all duration-300 disabled:opacity-30"
                            >
                                {{ cancelText }}
                            </button>
                        </div>
                    </div>

                    <!-- Decorative parchment corners -->
                    <div class="absolute top-0 left-0 w-12 h-12 border-t border-l border-white/5 pointer-events-none"></div>
                    <div class="absolute top-0 right-0 w-12 h-12 border-t border-r border-white/5 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-12 h-12 border-b border-l border-white/5 pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-12 h-12 border-b border-r border-white/5 pointer-events-none"></div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.font-cinzel { font-family: 'Cinzel', serif; }
.font-serif { font-family: 'Cormorant Garamond', serif; }

@keyframes modal-pop {
    0% { opacity: 0; transform: scale(0.95) translateY(10px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

.animate-modal-pop {
    animation: modal-pop 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
