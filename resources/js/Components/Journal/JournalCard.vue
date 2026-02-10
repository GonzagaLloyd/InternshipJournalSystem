<script setup>
import { Link } from '@inertiajs/vue3';


const props = defineProps({
    entry: Object
});

const formatDate = (dateString) => {
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric'
    }).format(new Date(dateString));
};
</script>

<template>
    <div class="group relative flex flex-col items-center text-center justify-center min-h-[350px] p-10 transition-all duration-1000 cursor-pointer block">
        <!-- Warm Ambient Background (Transparent as requested) -->
        <div class="absolute inset-0 bg-transparent transition-colors duration-700"></div>

        <!-- Ethereal Ancient Mist (Subtle Gold/Sepia) -->
        <div class="absolute inset-0 bg-[#d4af37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-1000 blur-3xl"></div>


        <!-- Ancient Border Frame -->
        <div class="absolute inset-4 opacity-0 group-hover:opacity-100 transition-opacity duration-1000 z-20">
            <!-- Main Frame with Breathing Glow -->
            <div class="absolute inset-0 border border-[#f4e4bc]/30 rounded-sm shadow-[0_0_15px_rgba(244,228,188,0.05)] group-hover:animate-ancient-pulse"></div>
            
            <!-- Corner Accents (Keep simple) -->
            <div class="absolute -top-[1px] -left-[1px] w-3 h-3 border-t border-l border-[#f4e4bc]/60"></div>
            <div class="absolute -top-[1px] -right-[1px] w-3 h-3 border-t border-r border-[#f4e4bc]/60"></div>
            <div class="absolute -bottom-[1px] -left-[1px] w-3 h-3 border-b border-l border-[#f4e4bc]/60"></div>
            <div class="absolute -bottom-[1px] -right-[1px] w-3 h-3 border-b border-r border-[#f4e4bc]/60"></div>
        </div>

        <!-- Ornamental Corners (Minimal Scribe Style) -->
        <div class="absolute inset-0 pointer-events-none z-30">
            <!-- Top Left -->
            <div class="absolute top-6 left-6 w-8 h-8 border-t-[1px] border-l-[1px] border-[#f4e4bc]/40 group-hover:border-[#f4e4bc]/80 transition-colors duration-1000"></div>
            <!-- Top Right -->
            <div class="absolute top-6 right-6 w-8 h-8 border-t-[1px] border-r-[1px] border-[#f4e4bc]/40 group-hover:border-[#f4e4bc]/80 transition-colors duration-1000"></div>
            <!-- Bottom Left -->
            <div class="absolute bottom-6 left-6 w-8 h-8 border-b-[1px] border-l-[1px] border-[#f4e4bc]/40 group-hover:border-[#f4e4bc]/80 transition-colors duration-1000"></div>
            <!-- Bottom Right -->
            <div class="absolute bottom-6 right-6 w-8 h-8 border-b-[1px] border-r-[1px] border-[#f4e4bc]/40 group-hover:border-[#f4e4bc]/80 transition-colors duration-1000"></div>
        </div>

        
        <!-- Header with Date -->
        <div class="relative z-10 mb-4 group-hover:-translate-y-1 transition-transform duration-700">
            <span class="text-[9px] font-serif tracking-[0.3em] text-[#8b2635] uppercase opacity-80">{{ formatDate(entry.entry_date) }}</span>
        </div>

        <!-- Title -->
        <h3 class="text-2xl text-[#f4e4bc] font-serif font-medium tracking-wider mb-6 line-clamp-1 group-hover:text-white transition-colors duration-700 relative z-10 px-4">
            {{ entry.title }}
        </h3>

        <!-- Decorative Divider -->
        <div class="w-8 h-[1px] bg-[#f4e4bc]/20 mb-6 group-hover:w-16 transition-all duration-700"></div>

        <!-- Excerpt -->
        <div class="relative z-10 mb-4 max-w-[90%]">
            <p class="text-[#d9c5a3] text-base leading-loose font-serif italic text-center opacity-70 line-clamp-3 group-hover:opacity-100 transition-opacity duration-700">
                {{ entry.content }}
            </p>
        </div>

        <!-- Media Indicators (Clean & Aesthetic) -->
        <div v-if="entry.image || entry.audio" class="relative z-10 flex items-center justify-center gap-5 mb-6 opacity-40 group-hover:opacity-90 transition-all duration-700">
             <div v-if="entry.image" class="text-[#f4e4bc]" title="Image Attached">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
             </div>
             <div v-if="entry.audio" class="text-[#f4e4bc]" title="Voice Note">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
             </div>
        </div>

        <!-- Read Entry Link -->
        <div class="relative z-10 opacity-0 group-hover:opacity-100 transition-all duration-700 translate-y-1 group-hover:translate-y-0">
            <span class="text-[9px] font-serif tracking-[0.25em] text-[#f4e4bc] uppercase border-b border-[#f4e4bc]/20 pb-0.5 hover:border-[#f4e4bc] transition-all">Read Entry</span>
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
        box-shadow: 0 0 10px rgba(244, 228, 188, 0.1), inset 0 0 10px rgba(244, 228, 188, 0.05); 
        border-color: rgba(244, 228, 188, 0.2); 
    }
    50% { 
        box-shadow: 0 0 20px rgba(244, 228, 188, 0.2), inset 0 0 15px rgba(244, 228, 188, 0.1); 
        border-color: rgba(244, 228, 188, 0.5); 
    }
}
.animate-ancient-pulse { animation: ancient-pulse 4s ease-in-out infinite; }
</style>
