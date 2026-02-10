<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

const { toasts, removeToast, addToast } = useToast();
const page = usePage();

// Watch for flash messages from Inertia
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        addToast(flash.success, 'success');
    }
    if (flash?.error) {
        addToast(flash.error, 'error');
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div class="fixed bottom-8 right-8 z-[200] flex flex-col gap-4 pointer-events-none w-full max-w-sm">
        <TransitionGroup 
            enter-active-class="transform transition duration-500 ease-out"
            enter-from-class="translate-y-4 opacity-0 scale-95 blur-sm"
            leave-active-class="transform transition duration-300 ease-in"
            leave-to-class="opacity-0 scale-95 blur-sm"
        >
            <div 
                v-for="toast in toasts" 
                :key="toast.id"
                class="pointer-events-auto relative group overflow-hidden"
            >
                <!-- Decorative Frame -->
                <div 
                    :class="[
                        toast.type === 'error' ? 'bg-[#1c1412] border-[#8b2635]/50' : 'bg-[#141210] border-[#d9c5a3]/20',
                        'relative bg-[#141210] border rounded-2xl p-5 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.5)] flex items-center gap-5 transition-all duration-300'
                    ]"
                >
                    <!-- Background Texture -->
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>

                    <!-- Ambient Glow -->
                    <div 
                        :class="[
                            toast.type === 'error' ? 'bg-[#8b2635]/10' : 'bg-[#d9c5a3]/5',
                            'absolute -inset-1 blur-xl opacity-0 group-hover:opacity-100 transition-opacity'
                        ]"
                    ></div>

                    <!-- Icon -->
                    <div 
                        :class="[
                            toast.type === 'error' ? 'bg-[#8b2635]/20 text-[#8b2635]' : 'bg-[#d9c5a3]/10 text-[#f4e4bc]',
                            'h-10 w-10 rounded-full flex items-center justify-center shrink-0 border border-current/20'
                        ]"
                    >
                        <svg v-if="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>

                    <!-- Message -->
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-[0.3em] font-black font-sans mb-0.5" 
                           :class="toast.type === 'error' ? 'text-[#8b2635]' : 'text-[#8c7e6a]'">
                            {{ toast.type === 'error' ? 'Correction' : 'Enlightenment' }}
                        </p>
                        <p class="text-[#f4e4bc] font-serif text-sm leading-relaxed truncate">
                            {{ toast.message }}
                        </p>
                    </div>

                    <!-- Close -->
                    <button 
                        @click="removeToast(toast.id)"
                        class="p-2 text-[#8c7e6a]/40 hover:text-[#f4e4bc] transition-colors shrink-0"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Auto-expire Progress -->
                    <div 
                        v-if="toast.duration > 0"
                        class="absolute bottom-0 left-0 h-[1px] bg-gradient-to-r from-transparent via-current to-transparent opacity-30 w-full"
                        :class="toast.type === 'error' ? 'text-[#8b2635]' : 'text-[#d9c5a3]'"
                        :style="{ animation: `progress ${toast.duration}ms linear forwards` }"
                    ></div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
