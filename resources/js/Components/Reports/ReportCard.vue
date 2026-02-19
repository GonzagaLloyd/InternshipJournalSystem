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
        class="group relative flex flex-col items-start transition-all duration-1000 cursor-pointer animate-report-enter hover:-translate-y-1 active:scale-[0.99] master-ledger-folio min-h-[340px]"
    >
        <!-- High-End Bronze Corner Indicators (Active Indication) -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <!-- Specific color match from provided image: #8B6D45 (Rich Bronze) -->
            <div class="absolute top-0 left-0 w-10 h-10 border-t border-l border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
            <div class="absolute top-0 right-0 w-10 h-10 border-t border-r border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
            <div class="absolute bottom-0 left-0 w-10 h-10 border-b border-l border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
            <div class="absolute bottom-0 right-0 w-10 h-10 border-b border-r border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
        </div>

        <!-- The Master Folio Stack (Atmospheric Reveal) -->
        <div class="absolute inset-0 bg-transparent opacity-0 group-hover:opacity-100 group-hover:bg-[#161616] z-0 shadow-none group-hover:shadow-2xl overflow-hidden ledger-surface border-l-[1px] border-transparent group-hover:border-[#A68B6A]/20 ring-0 group-hover:ring-1 group-hover:ring-white/5 transition-all duration-1000 ease-out">
            <!-- Precise System Texture (Matches BG Cover) -->
            <div class="absolute inset-0 opacity-[0.2] bg-[url('https://www.transparenttextures.com/patterns/dust.png')] pointer-events-none"></div>
            <!-- Binding Depth Shadow -->
            <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-black/80 via-black/30 to-transparent pointer-events-none"></div>
            <!-- Vertical Margin Line (Soft Brass Glow) -->
            <div class="absolute left-16 top-0 bottom-0 w-[0.5px] bg-gradient-to-b from-transparent via-[#A68B6A]/20 to-transparent pointer-events-none"></div>
        </div>

        <!-- Meta Header (Classification) -->
        <div class="relative z-10 w-full px-10 pt-10 pb-4 flex justify-between items-end">
            <div class="flex flex-col gap-1 opacity-20 group-hover:opacity-100 transition-opacity duration-1000">
                <span class="text-[7px] font-cinzel tracking-[0.6em] text-[#A68B6A] uppercase font-black">Chronicle Type</span>
                <span class="text-xs font-cinzel text-[#E3D5C1] tracking-[0.2em]">WEEKLY PROGRESS</span>
            </div>
            <div class="text-right flex flex-col gap-1 opacity-10 group-hover:opacity-100 transition-opacity duration-1000">
                <span class="text-[7px] font-cinzel tracking-[0.6em] text-[#A68B6A] uppercase font-black">Sealed On</span>
                <span class="text-[10px] font-serif italic text-[#C9B79C]">{{ report.created_at?.split(' ')[0] }} {{ report.created_at?.split(' ')[1] }}</span>
            </div>
        </div>

        <!-- Narrative Inscription -->
        <div class="relative z-10 px-10 pl-20 w-full flex-1 flex flex-col">
            <!-- Title: High-End Documentation Heading -->
            <h3 class="text-xl md:text-2xl text-[#E3D5C1]/40 group-hover:text-[#E3D5C1] font-cinzel font-medium tracking-tight mb-6 leading-snug max-w-full italic transition-all duration-1000">
                summary of chronicles...
            </h3>

            <!-- Narrative Content -->
            <div class="max-w-full mb-8 flex-1">
                <p class="text-[#C9B79C]/20 group-hover:text-[#C9B79C]/40 text-[15px] leading-[2.2] font-serif italic line-clamp-3 transition-all duration-1000">
                    a formal compilation of activities and technical implementations recorded from {{ report.period?.start }} through {{ report.period?.end }}.
                </p>
            </div>

            <!-- Footer Details -->
            <div class="mt-auto pb-10 flex items-center justify-between">
                <div class="flex items-center gap-6 opacity-0 group-hover:opacity-30 transition-all duration-1000 translate-y-2 group-hover:translate-y-0">
                    <div class="flex items-center gap-2 text-[#A68B6A] uppercase text-[7px] font-cinzel tracking-[0.4em]">
                         <div class="w-1 h-1 bg-[#A68B6A]"></div>
                         Vaulted Record
                    </div>
                </div>
                
                <!-- Silent Removal -->
                <button 
                    @click.stop="$emit('delete', report.id)"
                    class="p-2 text-void hover:text-red-500/60 transition-all duration-700 opacity-0 group-hover:opacity-100"
                    title="Strike from Vault"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Depth Stack Effect -->
        <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-black/40 to-transparent pointer-events-none z-10 opacity-0 group-hover:opacity-30 transition-opacity duration-1000"></div>
    </div>
</template>

<style scoped>
.ledger-surface {
    border-radius: 2px 10px 10px 2px;
}

.master-ledger-folio:hover .ledger-surface {
    background-color: #1a1a1a;
    box-shadow: 
        0 40px 80px -20px rgba(0, 0, 0, 0.7),
        inset 0 0 50px rgba(166, 139, 106, 0.04);
}

@keyframes report-enter {
    from { opacity: 0; transform: translateY(15px); filter: blur(4px); }
    to { opacity: 1; transform: translateY(0); filter: blur(0); }
}

.animate-report-enter {
    animation: report-enter 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.font-cinzel {
    font-family: 'Cinzel', serif;
}
</style>
