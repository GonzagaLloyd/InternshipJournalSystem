<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import JournalCard from '@/Components/Journal/JournalCard.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';

const props = defineProps({
    entries: Array
});

const searchQuery = ref('');
const isNavigating = ref(false);

const filteredEntries = computed(() => {
    if (!searchQuery.value) return props.entries;
    return props.entries.filter(entry => 
        entry.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        entry.content.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const handleEntryClick = (id) => {
    isNavigating.value = true;
    setTimeout(() => {
        router.visit(route('journal.show', id));
    }, 1500);
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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-12 lg:mb-16 relative z-10">
                <div class="relative group w-full md:w-auto text-center md:text-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-cinzel tracking-tight mb-3 transition-transform duration-700 group-hover:scale-[1.01]">
                        <span class="bg-gradient-to-b from-[#f4e4bc] via-[#d9c5a3] to-[#a8967a] bg-clip-text text-transparent drop-shadow-md">
                            Chronicler's Ledger
                        </span>
                    </h1>
                    <div class="flex items-center gap-4 justify-center md:justify-start">
                        <div class="h-[1px] flex-1 md:flex-none md:w-16 bg-gradient-to-r from-transparent via-[#8b2635]/60 to-transparent"></div>
                        <p class="text-[9px] sm:text-[10px] uppercase tracking-[0.4em] sm:tracking-[0.6em] text-[#8b2635] font-black font-sans opacity-95">Lore of Your Journeys</p>
                        <div class="h-[1px] flex-1 md:flex-none md:w-16 bg-gradient-to-r from-transparent via-[#8b2635]/60 to-transparent"></div>
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
            <div class="flex-1 overflow-y-auto pr-0 md:pr-2 scrollbar-hide relative z-10">
                <div v-if="filteredEntries.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-8 lg:gap-12 pb-20">
                    <JournalCard 
                        v-for="entry in filteredEntries" 
                        :key="entry.id"
                        :entry="entry"
                        @click="handleEntryClick(entry.id)"
                    />
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
    <TomeLoader :show="isNavigating" message="Consulting the Archives..." />
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
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
</style>
