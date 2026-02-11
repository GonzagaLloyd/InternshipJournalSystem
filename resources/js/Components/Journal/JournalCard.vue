<script setup>
import { Link } from '@inertiajs/vue3';


const props = defineProps({
    entry: Object
});

const emit = defineEmits(['delete']);

const formatDate = (dateString) => {
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric'
    }).format(new Date(dateString));
};
</script>

<template>
    <div class="group relative flex flex-col items-center text-center justify-center min-h-[350px] px-6 py-10 sm:p-10 transition-all duration-1000 cursor-pointer block">
        <!-- Warm Ambient Background (Transparent as requested) -->
        <div class="absolute inset-0 bg-transparent transition-colors duration-700"></div>

        <!-- Ethereal Ancient Mist (Subtle Brass/Sepia) -->
        <div class="absolute inset-0 bg-[#8C6A4A]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-1000 blur-3xl"></div>

        <!-- Strike Record Button (Delete) -->
        <button 
            @click.stop="$emit('delete', entry.id)"
            class="absolute top-8 right-8 z-40 p-2 text-red-500/20 hover:text-red-500 hover:bg-red-500/5 rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100"
            title="Strike from Ledger"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>


        <!-- Ancient Border Frame -->
        <div class="absolute inset-4 opacity-0 group-hover:opacity-100 transition-opacity duration-1000 z-20">
            <!-- Main Frame with Breathing Glow -->
            <div class="absolute inset-0 border border-[#C9B79C]/30 rounded-sm shadow-[0_0_15px_rgba(201,183,156,0.05)] group-hover:animate-ancient-pulse"></div>
            
            <!-- Corner Accents (Keep simple) -->
            <div class="absolute -top-[1px] -left-[1px] w-3 h-3 border-t border-l border-[#C9B79C]/60"></div>
            <div class="absolute -top-[1px] -right-[1px] w-3 h-3 border-t border-r border-[#C9B79C]/60"></div>
            <div class="absolute -bottom-[1px] -left-[1px] w-3 h-3 border-b border-l border-[#C9B79C]/60"></div>
            <div class="absolute -bottom-[1px] -right-[1px] w-3 h-3 border-b border-r border-[#C9B79C]/60"></div>
        </div>

        <!-- Ornamental Corners (Minimal Scribe Style) -->
        <div class="absolute inset-0 pointer-events-none z-30">
            <!-- Top Left -->
            <div class="absolute top-6 left-6 w-8 h-8 border-t-[1px] border-l-[1px] border-[#C9B79C]/40 group-hover:border-[#C9B79C]/80 transition-colors duration-1000"></div>
            <!-- Top Right -->
            <div class="absolute top-6 right-6 w-8 h-8 border-t-[1px] border-r-[1px] border-[#C9B79C]/40 group-hover:border-[#C9B79C]/80 transition-colors duration-1000"></div>
            <!-- Bottom Left -->
            <div class="absolute bottom-6 left-6 w-8 h-8 border-b-[1px] border-l-[1px] border-[#C9B79C]/40 group-hover:border-[#C9B79C]/80 transition-colors duration-1000"></div>
            <!-- Bottom Right -->
            <div class="absolute bottom-6 right-6 w-8 h-8 border-b-[1px] border-r-[1px] border-[#C9B79C]/40 group-hover:border-[#C9B79C]/80 transition-colors duration-1000"></div>
        </div>

        
        <!-- Header with Date -->
        <div class="relative z-10 mb-4 group-hover:-translate-y-1 transition-transform duration-700">
            <span class="text-[9px] font-serif tracking-[0.3em] text-[#8C6A4A] uppercase opacity-80">{{ formatDate(entry.entry_date) }}</span>
        </div>

        <!-- Title -->
        <h3 class="text-2xl text-[#C9B79C] font-serif font-medium tracking-wider mb-6 line-clamp-1 group-hover:text-white transition-colors duration-700 relative z-10 px-4">
            {{ entry.title }}
        </h3>

        <!-- Decorative Divider -->
        <div class="w-8 h-[1px] bg-[#C9B79C]/20 mb-6 group-hover:w-16 transition-all duration-700"></div>

        <!-- Excerpt -->
        <div class="relative z-10 mb-4 max-w-[90%]">
            <p class="text-[#C9B79C] text-base leading-loose font-serif italic text-center opacity-70 line-clamp-3 group-hover:opacity-100 transition-opacity duration-700">
                {{ entry.content }}
            </p>
        </div>

        <!-- Media Indicators (Clean & Aesthetic) -->
        <div v-if="entry.image || entry.audio" class="relative z-10 flex items-center justify-center gap-5 mb-6 opacity-40 group-hover:opacity-90 transition-all duration-700">
             <div v-if="entry.image" class="text-[#C9B79C]" title="Image Attached">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
             </div>
             <div v-if="entry.audio" class="text-[#C9B79C]" title="Voice Note">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
             </div>
        </div>

        <!-- Read Entry Link -->
        <div class="relative z-10 opacity-0 group-hover:opacity-100 transition-all duration-700 translate-y-1 group-hover:translate-y-0">
            <span class="text-[9px] font-serif tracking-[0.25em] text-[#C9B79C] uppercase border-b border-[#C9B79C]/20 pb-0.5 hover:border-[#C9B79C] transition-all">Read Entry</span>
        </div>
    </div>
</template>

<style scoped>
@keyframes breath {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.05); opacity: 1; }
}
.animate-breath {
    animation: breath 4s ease-in-out infinite;
}

@keyframes ancient-pulse {
    0%, 100% { 
        box-shadow: 0 0 10px rgba(201, 183, 156, 0.1), inset 0 0 10px rgba(201, 183, 156, 0.05); 
        border-color: rgba(201, 183, 156, 0.2); 
    }
    50% { 
        box-shadow: 0 0 20px rgba(201, 183, 156, 0.2), inset 0 0 15px rgba(201, 183, 156, 0.1); 
        border-color: rgba(201, 183, 156, 0.5); 
    }
}
.animate-ancient-pulse { animation: ancient-pulse 4s ease-in-out infinite; }
</style>
