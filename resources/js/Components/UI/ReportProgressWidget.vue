<template>
    <div v-if="isGenerating || (generationStep === 4 && resultData)" class="fixed top-24 right-8 z-[100] transition-all duration-500">
        <!-- Minimized View (Icon only) -->
        <div 
            v-if="isMinimized"
            @click="isMinimized = false"
            class="group relative w-14 h-14 bg-[#141414]/90 backdrop-blur-xl border border-[#A68B6A]/30 rounded-full flex items-center justify-center cursor-pointer shadow-[0_10px_30px_rgba(0,0,0,0.5)] hover:border-[#A68B6A] transition-all duration-500 animate-modal-pop"
        >
            <!-- Ambient Glow -->
            <div :class="[
                'absolute inset-0 blur-xl opacity-20 group-hover:opacity-40 transition-opacity rounded-full',
                generationStep === 4 ? 'bg-green-500' : 'bg-[#A68B6A]'
            ]"></div>

             <!-- Ritual Spinner (Minimized) -->
             <div class="relative w-10 h-10">
                 <div class="absolute inset-0 border border-[#A68B6A]/20 rounded-full animate-[spin_5s_linear_infinite]"></div>
                 <div class="absolute inset-1 border border-dashed border-[#A68B6A]/40 rounded-full animate-[spin_8s_linear_infinite_reverse]"></div>
                 <div class="absolute inset-0 flex items-center justify-center">
                     <svg 
                        class="w-5 h-5 transition-all duration-500" 
                        :class="[
                            generationStep < 4 ? 'text-[#A68B6A] animate-pulse' : 'text-green-500 scale-110'
                        ]"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    >
                         <path v-if="generationStep < 4" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                         <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                     </svg>
                 </div>
             </div>
             
             <!-- Tooltip (Fantasy Style) -->
             <div class="absolute right-full mr-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                 <div class="bg-[#141414] border border-[#A68B6A]/30 px-3 py-1.5 rounded-lg whitespace-nowrap">
                     <p class="text-[9px] font-cinzel text-[#A68B6A] uppercase tracking-[0.2em]">Ritual Status: {{ generationStep < 4 ? 'Weaving...' : 'Complete' }}</p>
                 </div>
             </div>
        </div>

        <!-- Expanded View (The card) -->
        <div v-else class="bg-[#141414]/90 backdrop-blur-xl border border-[#A68B6A]/30 rounded-2xl p-6 shadow-[0_40px_100px_rgba(0,0,0,0.8)] w-80 space-y-5 animate-modal-pop relative overflow-hidden">
            <!-- Subtle ambient glow -->
            <div :class="[
                'absolute -top-12 -right-12 w-32 h-32 blur-[60px] opacity-20 pointer-events-none transition-colors duration-1000',
                generationStep === 4 ? 'bg-green-500' : 'bg-[#A68B6A]'
            ]"></div>

            <div class="flex items-center justify-between relative z-10">
                <p class="text-[9px] uppercase tracking-[0.3em] text-[#A68B6A] font-black">Ritual of Synthesis</p>
                <div class="flex gap-2">
                    <button @click="isMinimized = true" class="text-[#A68B6A]/30 hover:text-[#A68B6A] transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                    </button>
                    <button v-if="generationStep === 4 || generationError" @click="clearStatus" class="text-[#A68B6A]/30 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4 relative z-10">
                <!-- Ritual Spinner -->
                <div class="relative shrink-0 w-12 h-12">
                    <div class="absolute inset-0 border border-[#A68B6A]/20 rounded-full animate-[spin_5s_linear_infinite]"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg 
                            class="w-5 h-5 transition-all duration-500" 
                            :class="[
                                generationStep < 4 ? 'text-[#A68B6A] animate-pulse' : 'text-green-500 scale-110'
                            ]"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path v-if="generationStep < 4" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <h4 class="text-[12px] font-cinzel text-[#E3D5C1] truncate mb-1">
                        {{ 
                            generationStep === 1 ? 'Reading Fragments...' : 
                            generationStep === 2 ? 'Consulting Archives...' : 
                            generationStep === 3 ? 'Weaving Chronicle...' : 
                            generationStep === 4 ? 'Divine Writing Complete' : 'Initializing...'
                        }}
                    </h4>
                    <p class="text-[10px] text-[#A68B6A]/60 font-serif italic">
                        {{ generationStep < 4 ? 'The Scribe is focused...' : 'A new decree awaits.' }}
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div v-if="generationStep < 4" class="h-[1px] w-full bg-white/5 rounded-full overflow-hidden relative">
                <div 
                    class="absolute top-0 left-0 h-full bg-gradient-to-r from-[#8C6A4A] to-[#C9B79C] transition-all duration-1000 ease-out"
                    :style="{ width: `${(generationStep / 3) * 100}%` }"
                ></div>
            </div>

            <!-- Final Action -->
            <div v-if="generationStep === 4" class="animate-fade-in-up pt-2">
                <button 
                    @click="viewDraft"
                    class="w-full relative group/btn py-3 px-6 rounded-xl overflow-hidden shadow-lg transition-all duration-500 transform hover:-translate-y-0.5"
                >
                    <div class="absolute inset-0 bg-[#A68B6A]"></div>
                    <div class="absolute inset-0 bg-white/20 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                    <span class="relative z-10 text-[10px] font-cinzel font-black text-[#1B1B1B] uppercase tracking-[0.2em]">
                        Examine Draft
                    </span>
                </button>
            </div>

            <!-- Error State -->
            <div v-if="generationError" class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl">
                <p class="text-[10px] text-red-400 italic font-serif leading-relaxed">
                    {{ generationError }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { watch } from 'vue';
import { useReportGeneration } from '@/Composables/useReportGeneration';
import { useToast } from '@/Composables/useToast';
import { router } from '@inertiajs/vue3';

const { 
    isGenerating, 
    generationStep, 
    generationError, 
    resultData, 
    isMinimized,
    clearStatus 
} = useReportGeneration();

const { success } = useToast();

// Trigger toast when done
let hasShownSuccess = false;
watch(() => generationStep.value, (newStep) => {
    if (newStep === 4 && !hasShownSuccess) {
        success('Divine Synthesis Complete: A new chronicle has been woven.');
        hasShownSuccess = true;
    } else if (newStep < 4) {
        hasShownSuccess = false;
    }
}, { immediate: true });

const viewDraft = () => {
    if (resultData.value) {
        router.post(route('reports.draft'), resultData.value);
        clearStatus();
    }
};
</script>

<style scoped>
.font-cinzel { font-family: 'Cinzel', serif; }
.font-serif { font-family: 'Cormorant Garamond', serif; }

@keyframes modal-pop {
    0% { opacity: 0; transform: scale(0.9) translateY(20px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

.animate-modal-pop {
    animation: modal-pop 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
