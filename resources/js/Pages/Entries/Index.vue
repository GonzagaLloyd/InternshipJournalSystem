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
        <div class="h-full bg-[#161412] p-4 md:p-6 lg:p-8 flex flex-col font-serif min-h-[calc(100vh-64px)] overflow-hidden">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                <div>
                    <h1 class="text-4xl font-black text-[#d9c5a3] italic tracking-tighter mb-2">The Archives</h1>
                    <p class="text-[10px] uppercase tracking-[0.4em] text-[#8b2635] font-black">History of your inscriptions</p>
                </div>

                <!-- Search Bar -->
                <div class="w-full md:w-96 relative group">
                    <input 
                        v-model="searchQuery"
                        type="search" 
                        placeholder="Search manuscripts..."
                        class="w-full bg-[#1c1a18] border border-[#3d352e] rounded-2xl px-6 py-4 text-[#d9c5a3] focus:ring-2 focus:ring-[#8b2635] focus:border-transparent transition-all placeholder:text-[#3d352e]"
                    />
                    <svg class="absolute right-6 top-1/2 -translate-y-1/2 w-5 h-5 text-[#3d352e] group-focus-within:text-[#8b2635] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Entries Grid -->
            <div class="flex-1 overflow-y-auto pr-2 scrollbar-hide">
                <div v-if="filteredEntries.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div 
                        v-for="entry in filteredEntries" 
                        :key="entry.id"
                        class="group bg-[#1c1a18] border border-[#3d352e] rounded-[2rem] p-8 hover:border-[#8b2635]/50 transition-all duration-500 relative overflow-hidden flex flex-col"
                    >
                        <!-- Parchment Texture Overlay -->
                        <div class="absolute inset-0 opacity-[0.02] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/parchment.png')] group-hover:opacity-[0.05] transition-opacity"></div>
                        
                        <!-- Date Badge -->
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-[9px] uppercase tracking-[0.3em] text-[#8b2635] font-black opacity-60">{{ formatDate(entry.entry_date) }}</span>
                            <div class="flex gap-2">
                                <span v-if="entry.image" class="h-1.5 w-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                                <span v-if="entry.audio" class="h-1.5 w-1.5 rounded-full bg-[#8b2635] shadow-[0_0_8px_rgba(139,38,53,0.5)]"></span>
                            </div>
                        </div>

                        <!-- Content Preview -->
                        <h3 class="text-xl font-black text-[#d9c5a3] italic mb-4 line-clamp-1 group-hover:text-white transition-colors">{{ entry.title }}</h3>
                        <p class="text-[#8c7e6a] text-sm leading-relaxed line-clamp-4 flex-1 mb-8 opacity-70 group-hover:opacity-100 transition-opacity">
                            {{ entry.content }}
                        </p>

                        <!-- Media Indicators / Footer -->
                        <div class="flex items-center justify-between pt-6 border-t border-[#3d352e]/30">
                            <div class="flex gap-3">
                                <span v-if="entry.image" class="text-[9px] uppercase tracking-widest text-[#8c7e6a] flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Visual
                                </span>
                                <span v-if="entry.audio" class="text-[9px] uppercase tracking-widest text-[#8c7e6a] flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                    Vocal
                                </span>
                            </div>
                            
                            <button class="text-[10px] uppercase tracking-[0.2em] font-black text-[#8b2635] hover:text-[#d9c5a3] transition-colors flex items-center gap-2">
                                Inspect
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex-1 flex flex-col items-center justify-center py-40 text-center">
                    <div class="h-20 w-20 rounded-full border border-dashed border-[#3d352e] flex items-center justify-center mb-8 opacity-20">
                        <svg class="w-10 h-10 text-[#8c7e6a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-[#d9c5a3] text-xl font-black italic mb-2 tracking-tighter">No manuscripts found</h3>
                    <p class="text-[#8c7e6a] text-[10px] uppercase tracking-widest font-bold">The archives are empty for this search query</p>
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
</style>
