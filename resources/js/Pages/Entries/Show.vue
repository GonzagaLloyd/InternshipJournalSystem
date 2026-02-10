<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import EntryMedia from '@/Components/Journal/EntryMedia.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';

const props = defineProps({
    entry: Object
});

const isReturning = ref(false);

const handleReturn = () => {
    console.log("Navigating back to ledger...");
    isReturning.value = true;
    
    // Explicit 1.5s delay to ensure the premium animation is witnessed
    setTimeout(() => {
        router.visit(route('journal.index'));
    }, 1500);
};

const formatDate = (dateString) => {
    return new Intl.DateTimeFormat('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    }).format(new Date(dateString));
};
</script>

<template>
    <Head :title="entry.title" />

    <div class="h-full bg-[#161412] text-[#d9c5a3] p-4 md:p-8 flex flex-col items-center relative overflow-hidden font-cormorant min-h-screen">
            
            <!-- Dust Specs Layer (Matched to Index) -->
            <div class="absolute inset-0 pointer-events-none z-20 overflow-hidden">
                <div class="dust-particles opacity-[0.1]"></div>
            </div>

            <!-- Background Texture (Matched to Index) -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>

            <!-- Back Navigation -->
            <div class="w-full max-w-4xl z-30 mb-8">
                 <a href="#" @click.prevent="handleReturn" class="inline-flex items-center gap-2 text-[#8c7e6a] hover:text-[#f4e4bc] transition-colors duration-300 group">
                    <div class="w-8 h-8 rounded-full border border-[#3d352e] flex items-center justify-center group-hover:border-[#8b2635]/60 transition-all">
                        <svg class="w-3 h-3 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Return to Ledger</span>
                </a>
            </div>

            <!-- Global Tome Loader -->
            <TomeLoader :show="isReturning" message="Preserving to Ledger..." />

            <!-- Main Entry Container -->
            <article class="w-full max-w-4xl relative z-30 bg-[#1e1b19]/40 backdrop-blur-[2px] border border-[#3d352e]/30 p-6 sm:p-8 md:p-12 lg:p-16 rounded-sm shadow-2xl animate-fade-in-up">
                
                <!-- Ornamental Border Corners (Responsive size) -->
                <div class="absolute top-0 left-0 w-8 sm:w-16 h-8 sm:h-16 border-t border-l border-[#8b2635]/20 rounded-tl-sm"></div>
                <div class="absolute top-0 right-0 w-8 sm:w-16 h-8 sm:h-16 border-t border-r border-[#8b2635]/20 rounded-tr-sm"></div>
                <div class="absolute bottom-0 left-0 w-8 sm:w-16 h-8 sm:h-16 border-b border-l border-[#8b2635]/20 rounded-bl-sm"></div>
                <div class="absolute bottom-0 right-0 w-8 sm:w-16 h-8 sm:h-16 border-b border-r border-[#8b2635]/20 rounded-br-sm"></div>

                <!-- Header -->
                <header class="text-center mb-10 sm:mb-16 relative">
                    <div class="mb-6 flex flex-col items-center gap-3">
                         <div class="h-8 sm:h-12 w-[1px] bg-gradient-to-b from-transparent via-[#8b2635]/40 to-transparent"></div>
                         <span class="text-[9px] sm:text-[11px] font-cinzel tracking-[0.3em] sm:tracking-[0.4em] text-[#8b2635] uppercase bg-[#8b2635]/5 px-3 sm:px-4 py-1 rounded-full border border-[#8b2635]/10 whitespace-nowrap">
                            {{ formatDate(entry.entry_date) }}
                        </span>
                    </div>
                    
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-cinzel font-bold text-[#f4e4bc] mb-6 sm:mb-8 leading-tight tracking-tight drop-shadow-sm px-2">
                        {{ entry.title }}
                    </h1>
                     <div class="w-16 sm:w-24 h-[1px] bg-gradient-to-r from-transparent via-[#f4e4bc]/30 to-transparent mx-auto"></div>
                </header>

                <!-- Content -->
                <div class="prose prose-lg sm:prose-xl prose-invert max-w-none text-[#d9c5a3] font-cormorant leading-relaxed tracking-wide text-justify selection:bg-[#8b2635]/20 selection:text-[#f4e4bc] mb-12 sm:mb-16">
                     <div class="first-letter:text-4xl sm:first-letter:text-5xl first-letter:font-cinzel first-letter:text-[#f4e4bc] first-letter:mr-2 sm:first-letter:mr-3 first-letter:float-left first-letter:leading-[0.8]">
                        <p class="whitespace-pre-wrap text-base sm:text-lg md:text-xl">{{ entry.content }}</p>
                    </div>
                </div>

                <!-- Media Section using Component -->
                <EntryMedia :entry="entry" />

                <!-- Footer -->
                 <div class="mt-12 sm:mt-20 pt-8 sm:pt-10 border-t border-[#3d352e]/40 flex flex-col items-center gap-6">
                    <div v-if="entry.file">
                        <a :href="'/storage/' + entry.file" target="_blank" class="flex items-center gap-3 text-[#8c7e6a] hover:text-[#f4e4bc] transition-all px-4 sm:px-5 py-2 border border-[#3d352e]/50 sm:border-transparent hover:border-[#3d352e] rounded-sm group">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            <span class="text-[10px] sm:text-xs font-cinzel tracking-widest uppercase">Attached Parchment</span>
                        </a>
                    </div>
                    
                    <div class="flex flex-col items-center opacity-40">
                        <div class="flex gap-2 mb-2">
                             <div class="w-1 h-1 rounded-full bg-[#f4e4bc]"></div>
                             <div class="w-1 h-1 rounded-full bg-[#f4e4bc]"></div>
                             <div class="w-1 h-1 rounded-full bg-[#f4e4bc]"></div>
                        </div>
                        <span class="text-[9px] font-cinzel uppercase tracking-[0.5em] text-[#f4e4bc]">Log End</span>
                    </div>
                </div>
            </article>
        </div>
</template>

<style scoped>
.dust-particles {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: 
        radial-gradient(1px 1px at 20px 30px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 40px 70px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 50px 160px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 90px 40px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 130px 80px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 160px 120px, #f4e4bc, rgba(0,0,0,0));
    background-repeat: repeat;
    background-size: 200px 200px;
    animation: dust 60s linear infinite;
}

@keyframes dust {
    from { transform: translateY(0); }
    to { transform: translateY(-200px); }
}

.ledger-shimmer {
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { filter: drop-shadow(0 0 2px rgba(139, 38, 53, 0.2)); transform: translateY(0); }
    50% { filter: drop-shadow(0 0 15px rgba(244, 228, 188, 0.3)); transform: translateY(-5px); }
}
</style>
