<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    entries: Array
});

const searchQuery = ref('');

const filteredEntries = computed(() => {
    if (!searchQuery.value) return props.entries;
    return props.entries.filter(entry => 
        entry.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        entry.content.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
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
    <Head title="Journal Entries" />

    <AuthenticatedLayout>
        <div class="h-full bg-[#161412] p-4 md:p-6 lg:p-8 flex flex-col font-cormorant min-h-[calc(100vh-64px)] overflow-hidden relative">
            <!-- Dust Specs Layer -->
            <div class="absolute inset-0 pointer-events-none z-20 overflow-hidden">
                <div class="dust-particles opacity-[0.1]"></div>
            </div>

            <!-- Background Texture Overlay -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-16 relative z-10">
                <div class="relative group">
                    <h1 class="text-6xl font-bold font-cinzel tracking-tight mb-3 transition-transform duration-700 group-hover:scale-[1.01]">
                        <span class="bg-gradient-to-b from-[#f4e4bc] via-[#d9c5a3] to-[#a8967a] bg-clip-text text-transparent drop-shadow-md">
                            Chronicler's Ledger
                        </span>
                    </h1>
                    <div class="flex items-center gap-4">
                        <div class="h-[1px] flex-1 min-w-[40px] bg-gradient-to-r from-transparent via-[#8b2635]/60 to-transparent"></div>
                        <p class="text-[10px] uppercase tracking-[0.6em] text-[#8b2635] font-black font-sans opacity-95">Lore of Your Journeys</p>
                        <div class="h-[1px] flex-1 min-w-[40px] bg-gradient-to-r from-transparent via-[#8b2635]/60 to-transparent"></div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="w-full md:w-96 relative group">
                    <input 
                        v-model="searchQuery"
                        type="search" 
                        placeholder="Seek through the echoes..."
                        class="w-full bg-[#1e1b19] border border-[#3d352e]/50 rounded-sm px-6 py-4 text-[#f4e4bc] font-cormorant text-xl focus:ring-0 focus:border-[#8b2635]/60 transition-all placeholder:text-[#3d352e] italic relative z-10"
                    />
                    <svg class="absolute right-6 top-1/2 -translate-y-1/2 w-5 h-5 text-[#3d352e] group-focus-within:text-[#8b2635] transition-colors z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Entries Grid -->
            <div class="flex-1 overflow-y-auto pr-2 scrollbar-hide relative z-10">
                <div v-if="filteredEntries.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-12 pb-20">
                    <div 
                        v-for="entry in filteredEntries" 
                        :key="entry.id"
                        class="group relative flex flex-col min-h-[400px] p-10 transition-all duration-1000"
                    >
                        <!-- Ethereal Glow Behind (Softer) -->
                        <div class="absolute -inset-1 bg-[#8b2635]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-1000 blur-2xl"></div>

                        <!-- Ancient Card Base -->
                        <div class="absolute inset-0 bg-[#1e1b19] border border-[#3d352e]/40 shadow-[0_20px_40px_rgba(0,0,0,0.4)] transition-all duration-700 group-hover:border-[#f4e4bc]/30"></div>
                        
                        <!-- Lightning Border Effect -->
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-[1.5px] bg-gradient-to-r from-transparent via-[#f4e4bc] to-transparent animate-lightning-x"></div>
                            <div class="absolute bottom-0 left-0 w-full h-[1.5px] bg-gradient-to-r from-transparent via-[#f4e4bc] to-transparent animate-lightning-x-rev"></div>
                            <div class="absolute top-0 left-0 h-full w-[1.5px] bg-gradient-to-b from-transparent via-[#f4e4bc] to-transparent animate-lightning-y"></div>
                            <div class="absolute top-0 right-0 h-full w-[1.5px] bg-gradient-to-b from-transparent via-[#f4e4bc] to-transparent animate-lightning-y-rev"></div>
                            
                            <!-- Outer Glow -->
                            <div class="absolute inset-x-0 top-0 h-4 bg-[#f4e4bc]/5 blur-md animate-lightning-flicker"></div>
                            <div class="absolute inset-x-0 bottom-0 h-4 bg-[#f4e4bc]/5 blur-md animate-lightning-flicker"></div>
                        </div>

                        <!-- Ornamental Corners (Clean & Simple) -->
                        <div class="absolute top-0 left-0 w-12 h-12">
                            <div class="absolute top-4 left-4 w-6 h-6 border-t border-l border-[#3d352e] group-hover:border-[#8b2635]/50 transition-colors duration-700"></div>
                            <div class="absolute top-3 left-3 w-1.5 h-1.5 border-t border-l border-[#8b2635]/40"></div>
                        </div>
                        <div class="absolute top-0 right-0 w-12 h-12 rotate-90">
                            <div class="absolute top-4 left-4 w-6 h-6 border-t border-l border-[#3d352e] group-hover:border-[#8b2635]/50 transition-colors duration-700"></div>
                            <div class="absolute top-3 left-3 w-1.5 h-1.5 border-t border-l border-[#8b2635]/40"></div>
                        </div>
                        <div class="absolute bottom-0 left-0 w-12 h-12 -rotate-90">
                            <div class="absolute top-4 left-4 w-6 h-6 border-t border-l border-[#3d352e] group-hover:border-[#8b2635]/50 transition-colors duration-700"></div>
                            <div class="absolute top-3 left-3 w-1.5 h-1.5 border-t border-l border-[#8b2635]/40"></div>
                        </div>
                        <div class="absolute bottom-0 right-0 w-12 h-12 rotate-180">
                            <div class="absolute top-4 left-4 w-6 h-6 border-t border-l border-[#3d352e] group-hover:border-[#8b2635]/50 transition-colors duration-700"></div>
                            <div class="absolute top-3 left-3 w-1.5 h-1.5 border-t border-l border-[#8b2635]/40"></div>
                        </div>

                        <!-- Manuscript Texture Overlay -->
                        <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/leather.png')] group-hover:opacity-[0.05] transition-opacity duration-700"></div>
                        
                        <!-- Header with Date and Breathing Seal -->
                        <div class="flex justify-between items-start mb-10 relative z-10">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-sans uppercase tracking-[0.4em] text-[#8b2635] font-black opacity-90 mb-2">{{ formatDate(entry.entry_date) }}</span>
                                <div class="h-[1px] w-16 bg-gradient-to-r from-[#8b2635] via-[#8b2635]/10 to-transparent"></div>
                            </div>
                            
                            <!-- Relic-style Seal -->
                            <div class="relative group/seal">
                                <div class="w-10 h-10 flex items-center justify-center relative">
                                    <svg class="absolute inset-0 text-[#8b2635]/10 group-hover:text-[#8b2635]/20 transition-all duration-700 animate-breath" viewBox="0 0 100 100">
                                        <path d="M50 5 Q75 5 95 30 T85 70 T50 95 T10 75 T5 40 T30 5 Z" fill="currentColor" />
                                    </svg>
                                    <div v-if="entry.image || entry.audio" class="relative z-10">
                                        <svg class="w-4 h-4 text-[#8b2635]/80 group-hover:text-[#8b2635]" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="5" fill="none" stroke="currentColor" stroke-width="1" />
                                            <circle cx="12" cy="12" r="2.5" />
                                        </svg>
                                    </div>
                                    <div v-else class="w-1.5 h-1.5 rounded-full bg-[#3d352e] group-hover:bg-[#8b2635]/60 transition-colors"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="text-3xl font-bold text-[#f4e4bc] font-cinzel italic mb-6 line-clamp-1 group-hover:text-white transition-all duration-500 leading-tight tracking-wide relative z-10">
                            {{ entry.title }}
                        </h3>

                        <!-- Excerpt -->
                        <div class="flex-1 relative z-10 mb-10">
                            <p class="text-[#8c7e6a] text-xl leading-relaxed line-clamp-4 opacity-80 group-hover:opacity-100 transition-opacity duration-700 font-medium italic">
                                "{{ entry.content }}"
                            </p>
                        </div>

                        <!-- Clean Epic Footer -->
                        <div class="flex items-center justify-between pt-8 border-t border-[#3d352e]/20 relative z-10">
                            <div class="flex gap-4">
                                <div v-if="entry.image" class="text-[#8c7e6a]/50 hover:text-[#f4e4bc] transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div v-if="entry.audio" class="text-[#8c7e6a]/50 hover:text-[#f4e4bc] transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                </div>
                            </div>
                            
                            <button class="group/btn relative flex items-center gap-3 py-1 ring-0 outline-none">
                                <span class="text-[10px] uppercase tracking-[0.5em] font-black text-[#8b2635] group-hover/btn:text-[#f4e4bc] transition-all duration-700 font-sans">Examine Legend</span>
                                <div class="w-8 h-[1px] bg-[#8b2635]/30 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-[#f4e4bc]/60 -translate-x-full group-hover/btn:translate-x-0 transition-transform duration-700"></div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex-1 flex flex-col items-center justify-center py-40 text-center relative z-10">
                    <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                        <svg class="w-[500px] h-[500px] text-[#f4e4bc] animate-pulse-slow" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                    </div>
                    <div class="relative">
                        <div class="h-24 w-24 rounded-full border border-[#3d352e]/50 flex items-center justify-center mb-10 mx-auto group">
                            <svg class="w-12 h-12 text-[#3d352e] group-hover:text-[#8b2635] transition-all duration-1000" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-[#f4e4bc] text-5xl font-cinzel font-bold mb-4 tracking-tight">The Ledger is Silent</h3>
                        <p class="text-[#8c7e6a] text-sm uppercase tracking-[0.5em] font-sans opacity-40">No echoes found in this search</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@keyframes breath {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.05); opacity: 1; }
}
.animate-breath {
    animation: breath 4s ease-in-out infinite;
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.02; transform: scale(1); }
    50% { opacity: 0.05; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 10s ease-in-out infinite;
}

/* Dust Particle Animation */
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
@keyframes lightning-x {
    0% { transform: translateX(-100%); opacity: 0; }
    10%, 20% { opacity: 1; }
    30% { opacity: 0.3; }
    40% { opacity: 1; }
    100% { transform: translateX(100%); opacity: 0; }
}
@keyframes lightning-y {
    0% { transform: translateY(-100%); opacity: 0; }
    15%, 25% { opacity: 1; }
    35% { opacity: 0.2; }
    45% { opacity: 1; }
    100% { transform: translateY(100%); opacity: 0; }
}
@keyframes lightning-flicker {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.8; }
    55% { opacity: 0.4; }
    60% { opacity: 0.9; }
}
.animate-lightning-x { animation: lightning-x 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite; }
.animate-lightning-x-rev { animation: lightning-x 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite reverse; }
.animate-lightning-y { animation: lightning-y 1.3s cubic-bezier(0.4, 0, 0.2, 1) infinite; }
.animate-lightning-y-rev { animation: lightning-y 1.4s cubic-bezier(0.4, 0, 0.2, 1) infinite reverse; }
.animate-lightning-flicker { animation: lightning-flicker 2s step-end infinite; }
</style>
