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

// Premium Folio Indexing (System Standard)
const folioCode = props.entry.id ? props.entry.id.toString().slice(-4).toUpperCase() : 'LX';
</script>

<template>
    <div class="group relative flex flex-col items-start transition-all duration-1000 cursor-pointer animate-card-enter hover:-translate-y-1 active:scale-[0.99] master-ledger-folio min-h-[380px]">
        
        <!-- High-End Bronze Corner Indicators (Active Indication) -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <!-- Specific color match from provided image: #8B6D45 (Rich Bronze) -->
            <div class="absolute top-0 left-0 w-10 h-10 border-t border-l border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
            <div class="absolute top-0 right-0 w-10 h-10 border-t border-r border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
            <div class="absolute bottom-0 left-0 w-10 h-10 border-b border-l border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
            <div class="absolute bottom-0 right-0 w-10 h-10 border-b border-r border-[#8B6D45]/30 group-hover:opacity-0 transition-all duration-700 ease-out"></div>
        </div>

        <!-- The Exalted Folio Stack (Atmospheric Reveal) -->
        <div class="absolute inset-0 bg-transparent opacity-0 group-hover:opacity-100 group-hover:bg-void/5 z-0 shadow-none group-hover:shadow-2xl overflow-hidden ledger-surface transition-all duration-1000 ease-out">
            <!-- Precise Vellum Texture -->
            <div class="absolute inset-0 opacity-[0.2] bg-[url('https://www.transparenttextures.com/patterns/dust.png')] pointer-events-none"></div>
            
            <!-- Ledger Ruling Lines -->
            <div class="absolute inset-0 ledger-lines opacity-0 group-hover:opacity-100 transition-opacity duration-1000 delay-300 pointer-events-none"></div>
            
            <!-- Ethereal Lighting -->
            <div class="ethereal-glint opacity-0 group-hover:opacity-100 duration-1000"></div>
            
            <!-- Leather Bound Spine Binding -->
            <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-black/80 via-black/20 to-transparent pointer-events-none border-l-[1px] border-brass/10"></div>
            
            <!-- Precise Binding Rivets -->
            <div class="absolute left-[8px] top-[20%] w-[1.5px] h-[1.5px] rounded-full bg-brass/30"></div>
            <div class="absolute left-[8px] top-[50%] w-[1.5px] h-[1.5px] rounded-full bg-brass/30"></div>
            <div class="absolute left-[8px] top-[80%] w-[1.5px] h-[1.5px] rounded-full bg-brass/30"></div>

            <!-- Single Brass Margin Thread -->
            <div class="absolute left-14 top-8 bottom-8 w-[0.5px] bg-gradient-to-b from-transparent via-brass/20 to-transparent"></div>
        </div>

        <!-- Meta Header -->
        <div class="relative z-10 w-full px-10 pt-10 pb-4 flex justify-between items-end">
            <div class="flex flex-col gap-1 opacity-20 group-hover:opacity-60 transition-opacity duration-1000">
                <span class="text-[7px] font-cinzel tracking-[0.6em] text-brass uppercase font-black">Folio Index</span>
                <span class="text-xs font-cinzel text-cream tracking-[0.2em] font-light">№ {{ folioCode }}</span>
            </div>
            <div class="text-right flex flex-col gap-1 opacity-10 group-hover:opacity-60 transition-opacity duration-1000">
                <span class="text-[7px] font-cinzel tracking-[0.6em] text-brass uppercase font-black">Recorded On</span>
                <span class="text-[10px] font-serif italic text-parchment tracking-wide">{{ formatDate(entry.entry_date) }}</span>
            </div>
        </div>

        <!-- Narrative Inscription -->
        <div class="relative z-10 px-10 pl-20 w-full flex-1 flex flex-col">
            <!-- Title -->
            <h3 class="text-xl md:text-2xl text-cream/20 group-hover:text-cream font-cinzel font-medium tracking-tight mb-8 leading-[32px] max-w-full truncate transition-all duration-1000">
                {{ entry.title }}
            </h3>

            <!-- Narrative Content -->
            <div class="max-w-full mb-8 flex-1">
                <p class="text-parchment/10 group-hover:text-parchment/40 text-[15px] leading-[32px] font-serif italic line-clamp-4 transition-all duration-1000">
                    {{ entry.content }}
                </p>
            </div>

            <!-- Footer Details -->
            <div class="mt-auto pb-10 flex items-center justify-between">
                <div class="flex items-center gap-6 opacity-0 group-hover:opacity-20 transition-all duration-1000 translate-y-1 group-hover:translate-y-0">
                    <div v-if="entry.image || entry.audio" class="flex items-center gap-2 text-brass uppercase text-[7px] font-cinzel tracking-[0.4em]">
                         <div class="w-1.5 h-[1px] bg-brass/20"></div>
                         Lore Fragment
                    </div>
                </div>
                
                <!-- Silent Removal -->
                <button 
                    @click.stop="$emit('delete', entry.id)"
                    class="p-2 text-void group-hover:text-red-500/10 transition-all duration-700 opacity-0 group-hover:opacity-100 hover:scale-105 active:scale-95"
                    title="Strike from Archives"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Subtle Spine Ambient Glow -->
        <div class="absolute top-0 left-0 bottom-0 w-[0.5px] bg-gradient-to-b from-transparent via-brass/10 to-transparent pointer-events-none z-10 opacity-0 group-hover:opacity-30 transition-opacity duration-1000"></div>
    </div>
</template>

<style scoped>
.ledger-surface {
    background-color: var(--color-background-obsidian);
    border-radius: 4px 0 0 4px;
    transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1);
    /* Clean Deckled Edge Mask */
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25'%3E%3Cfilter id='deckle'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.04' numOctaves='5' result='noise'/%3E%3CfeDisplacementMap in='SourceGraphic' in2='noise' scale='3' xChannelSelector='R' yChannelSelector='G'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' fill='white' filter='url(%23deckle)'/%3E%3C/svg%3E");
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25'%3E%3Cfilter id='deckle'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.04' numOctaves='5' result='noise'/%3E%3CfeDisplacementMap in='SourceGraphic' in2='noise' scale='3' xChannelSelector='R' yChannelSelector='G'/%3E%3C/filter%3E%3Crect width='99%25' height='100%25' fill='white' filter='url(%23deckle)'/%3E%3C/svg%3E");
}

.ledger-lines {
    background-image: repeating-linear-gradient(
        transparent,
        transparent 31px,
        rgba(166, 139, 106, 0.03) 31px,
        rgba(166, 139, 106, 0.03) 32px
    );
    background-size: 100% 32px;
    mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
}

.master-ledger-folio:hover .ledger-surface {
    background-color: #1a1a1a;
    box-shadow: 
        0 40px 80px -20px rgba(0, 0, 0, 0.9),
        inset 0 0 100px rgba(0, 0, 0, 0.5),
        inset -20px 0 60px rgba(166, 139, 106, 0.02);
}

.ethereal-glint {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        125deg,
        transparent 40%,
        rgba(166, 139, 106, 0.04) 50%,
        transparent 60%
    );
    background-size: 200% 200%;
    background-position: 100% 100%;
    transition: background-position 1.5s var(--transition-premium);
}

.master-ledger-folio:hover .ethereal-glint {
    background-position: 0% 0%;
}

@keyframes card-enter {
    from { opacity: 0; transform: translateY(15px); filter: blur(6px); }
    to { opacity: 1; transform: translateY(0); filter: blur(0); }
}

.animate-card-enter {
    animation: card-enter 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.font-cinzel {
    font-family: 'Cinzel', serif;
}
</style>











