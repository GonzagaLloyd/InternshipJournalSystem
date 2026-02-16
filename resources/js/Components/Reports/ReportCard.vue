<script setup>
defineProps({
    report: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['click', 'delete']);
</script>

<template>
    <div 
        @click="$emit('click', report)"
        class="group relative group/card cursor-pointer flex flex-col items-center justify-center min-h-[300px] transition-all duration-1000"
    >
        <!-- Banish to Vault Button (Delete) -->
        <button 
            @click.stop="$emit('delete', report.id)"
            class="absolute top-6 right-6 z-40 p-2 text-red-500/10 hover:text-red-500/80 hover:bg-red-500/5 rounded-full transition-all duration-500 opacity-0 group-hover/card:opacity-100"
            title="Banish to the Sunken Vault"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>

        <!-- Minimalist Thin Corners -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-0 w-8 h-8 border-t border-l border-[#A68B6A]/10 transition-all duration-1000 group-hover/card:border-[#A68B6A]/30 group-hover/card:w-10 group-hover/card:h-10"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t border-r border-[#A68B6A]/10 transition-all duration-1000 group-hover/card:border-[#A68B6A]/30 group-hover/card:w-10 group-hover/card:h-10"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b border-l border-[#A68B6A]/10 transition-all duration-1000 group-hover/card:border-[#A68B6A]/30 group-hover/card:w-10 group-hover/card:h-10"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b border-r border-[#A68B6A]/10 transition-all duration-1000 group-hover/card:border-[#A68B6A]/30 group-hover/card:w-10 group-hover/card:h-10"></div>
        </div>

        <!-- Shadow Depth Reveal & Inner Tint -->
        <div class="absolute inset-2 bg-[#0D0D0D]/10 transition-all duration-1000 group-hover/card:bg-[#0D0D0D]/30 group-hover/card:shadow-[0_0_60px_rgba(0,0,0,0.6)] group-hover/card:scale-[1.01]"></div>

        <div class="relative z-10 px-10 md:px-16 py-12 md:py-14 flex flex-col items-center text-center transition-all duration-1000">
            <!-- Top Month Label -->
            <span class="text-[9px] uppercase tracking-[0.8em] text-[#A68B6A]/50 font-black font-sans mb-8 group-hover/card:text-[#A68B6A] transition-all duration-1000">
                {{ (report.created_at || '').split(' ')[0] }}
            </span>

            <!-- Cinematic Lowercase Title -->
            <h4 class="text-3xl md:text-4xl font-serif text-[#E3D5C1]/80 group-hover/card:text-[#E3D5C1] transition-all duration-1000 leading-tight lowercase mb-5">
                weekly progress...
            </h4>

            <!-- Elegant line-node-line divider -->
            <div class="flex items-center gap-4 w-full max-w-[100px] mb-6">
                <div class="flex-1 h-[0.5px] bg-[#A68B6A]/20 transition-all duration-1000 group-hover/card:bg-[#A68B6A]/50"></div>
                <div class="w-1 h-1 rounded-full bg-[#A68B6A]/40 group-hover/card:bg-[#A68B6A] transition-all duration-1000"></div>
                <div class="flex-1 h-[0.5px] bg-[#A68B6A]/20 transition-all duration-1000 group-hover/card:bg-[#A68B6A]/50"></div>
            </div>

            <!-- Italicized Period Info -->
            <p class="text-base md:text-lg italic font-serif text-[#E3D5C1]/20 group-hover/card:text-[#E3D5C1]/50 transition-colors duration-1000 max-w-sm leading-relaxed lowercase mb-10">
                archive of {{ report.period?.start }} — {{ report.period?.end }}
            </p>
            
            <!-- Load Footer -->
            <div class="pt-4 opacity-40 group-hover/card:opacity-100 transition-all duration-1000 transform translate-y-1 group-hover/card:translate-y-0 flex flex-col items-center">
                <div class="w-20 h-[0.5px] bg-[#A68B6A]/20 mb-3 transition-all group-hover/card:w-24 group-hover/card:bg-[#A68B6A]/40"></div>
                <span class="text-[8px] uppercase tracking-[0.6em] text-[#A68B6A] font-black">Load Chronicle</span>
            </div>
        </div>
    </div>
</template>
