<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    availableEntries: Array
});

const emit = defineEmits(['generated']);

const selectedIds = ref([]);
const collapsedWeeks = ref([]);

const toggleWeek = (week) => {
    const index = collapsedWeeks.value.indexOf(week);
    if (index === -1) {
        collapsedWeeks.value.push(week);
    } else {
        collapsedWeeks.value.splice(index, 1);
    }
};

const groupedEntries = computed(() => {
    if (!props.availableEntries) return {};
    
    return props.availableEntries.reduce((groups, entry) => {
        const date = new Date(entry.entry_date);
        
        // Get the start of the week (Sunday)
        const startOfWeek = new Date(date);
        startOfWeek.setDate(date.getDate() - date.getDay());
        
        const weekKey = startOfWeek.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });
        
        if (!groups[weekKey]) {
            groups[weekKey] = [];
        }
        groups[weekKey].push(entry);
        return groups;
    }, {});
});

const toggleEntry = (id) => {
    const index = selectedIds.value.indexOf(id);
    if (index === -1) {
        selectedIds.value.push(id);
    } else {
        selectedIds.value.splice(index, 1);
    }
};

const toggleAll = () => {
    if (selectedIds.value.length === props.availableEntries.length) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.availableEntries.map(e => e.id);
    }
};

const isGenerating = ref(false);
const error = ref(null);

const generateReport = async () => {
    if (selectedIds.value.length === 0) {
        error.value = 'Please select at least one fragment to weave.';
        return;
    }

    isGenerating.value = true;
    error.value = null;
    
    try {
        const response = await axios.post(route('reports.generate'), {
            entry_ids: selectedIds.value
        });
        
        emit('generated', response.data);
    } catch (err) {
        error.value = err.response?.data?.error || 'The Grand Scribe is silent...';
    } finally {
        isGenerating.value = false;
    }
};
</script>

<template>
    <div class="relative group/generator flex flex-col h-full">
        <!-- Background Card Effect -->
        <div class="absolute inset-0 bg-[#1A1A1A] rounded-3xl border border-white/[0.05] shadow-2xl"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')] opacity-[0.02] rounded-3xl pointer-events-none"></div>
        
        <div class="relative p-8 md:p-10 flex flex-col h-full space-y-6">
            <div class="space-y-2 shrink-0">
                <p class="text-[10px] uppercase tracking-[0.5em] text-[#A68B6A] font-bold">Arcane Calculation</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[#E3D5C1] font-cinzel">Report Generator</h2>
                <p class="text-[#E3D5C1]/40 text-sm italic">Select the fragmented memories to be woven into a chronicle.</p>
            </div>

            <!-- Bulk Toggle Header -->
            <div class="flex justify-between items-center px-1 shrink-0">
                <label class="text-[11px] uppercase tracking-widest text-[#A68B6A]/60 font-black">Archive Fragments</label>
                <button 
                    @click="toggleAll"
                    class="text-[10px] uppercase tracking-widest text-[#A68B6A] hover:text-[#E3D5C1] font-bold transition-colors py-1 px-2 bg-white/[0.03] rounded-lg border border-white/[0.05]"
                >
                    {{ selectedIds.length === availableEntries?.length ? 'Deselect All' : 'Select All' }}
                </button>
            </div>

            <!-- Scrollable Selection Area -->
            <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar min-h-0">
                <div v-if="!availableEntries || availableEntries.length === 0" class="py-12 text-center">
                    <p class="text-[#A68B6A]/30 text-xs italic">No entries found in the archives.</p>
                </div>
                
                <div v-for="(entries, week) in groupedEntries" :key="week" class="mb-8 last:mb-0">
                    <!-- Week Header -->
                    <button 
                        @click="toggleWeek(week)" 
                        class="w-full flex items-center gap-4 mb-4 group/week focus:outline-none cursor-pointer"
                    >
                        <div class="h-[1px] flex-1 bg-gradient-to-r from-transparent to-[#A68B6A]/20 group-hover/week:to-[#A68B6A]/40 transition-colors"></div>
                        
                        <div class="flex items-center gap-2 text-[#A68B6A] group-hover/week:text-[#E3D5C1] transition-colors">
                            <svg 
                                class="w-3 h-3 transition-transform duration-300" 
                                :class="{'rotate-[-90deg]': collapsedWeeks.includes(week)}"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <h3 class="text-xs uppercase tracking-[0.3em] font-black whitespace-nowrap">
                                Week of {{ week }}
                            </h3>
                            <span class="text-[10px] opacity-60 font-serif italic">({{ entries.length }})</span>
                        </div>

                        <div class="h-[1px] flex-1 bg-gradient-to-l from-transparent to-[#A68B6A]/20 group-hover/week:to-[#A68B6A]/40 transition-colors"></div>
                    </button>

                    <div v-show="!collapsedWeeks.includes(week)" class="space-y-3">
                        <div 
                            v-for="entry in entries" 
                            :key="entry.id"
                            @click="toggleEntry(entry.id)"
                            :class="[
                                selectedIds.includes(entry.id) 
                                ? 'bg-[#A68B6A]/10 border-[#A68B6A]/40 shadow-[0_0_15px_rgba(166,139,106,0.05)]' 
                                : 'bg-white/[0.02] border-white/[0.05] hover:bg-white/[0.04]'
                            ]"
                            class="p-4 rounded-2xl border transition-all cursor-pointer group/item relative overflow-hidden"
                        >
                            <!-- Selection Indicator -->
                            <div 
                                :class="selectedIds.includes(entry.id) ? 'scale-100 opacity-100' : 'scale-0 opacity-0'"
                                class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 bg-[#A68B6A] rounded-full flex items-center justify-center transition-all duration-300"
                            >
                                <svg class="w-3 h-3 text-[#1B1B1B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <div class="pr-10">
                                <p class="text-[10px] uppercase tracking-widest text-[#A68B6A]/60 font-black mb-1">
                                    {{ entry.entry_date }}
                                </p>
                                <h4 class="text-[#E3D5C1] font-bold text-sm truncate">
                                    {{ entry.title || '(Untitled Fragment)' }}
                                </h4>
                            </div>

                            <!-- Subtle Underline on hover -->
                            <div 
                                class="absolute bottom-0 left-0 h-[2px] bg-[#A68B6A] transition-all duration-500"
                                :style="{ width: selectedIds.includes(entry.id) ? '100%' : '0%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="error" class="bg-red-500/10 border border-red-500/20 text-red-500/80 px-4 py-3 rounded-xl text-xs italic flex items-center gap-3 shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ error }}
            </div>

            <button 
                @click="generateReport"
                :disabled="isGenerating || selectedIds.length === 0"
                class="w-full h-14 bg-[#A68B6A] hover:bg-[#C9B79C] disabled:bg-[#A68B6A]/20 disabled:cursor-not-allowed text-[#1B1B1B] font-black uppercase tracking-[0.2em] text-xs rounded-xl transition-all shadow-xl shadow-[#A68B6A]/5 flex items-center justify-center gap-3 active:scale-[0.98] shrink-0"
            >
                <svg v-if="isGenerating" class="animate-spin h-4 w-4 text-[#1B1B1B]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>{{ isGenerating ? 'Summoning the Scribe...' : `Weave ${selectedIds.length} Fragments` }}</span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(166, 139, 106, 0.1);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(166, 139, 106, 0.3);
}
</style>
