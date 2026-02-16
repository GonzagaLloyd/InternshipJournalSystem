<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { useReportGeneration } from '@/Composables/useReportGeneration';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
    availableEntries: Array
});

const emit = defineEmits(['generated']);

const { 
    isGenerating, 
    generationStep, 
    startGeneration, 
    finishGeneration, 
    setError, 
    setStep, 
    clearStatus,
    weaveFragments,
    currentJobId
} = useReportGeneration();

const { success, error: toastError } = useToast();

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

// Initialize collapsedWeeks: Keep the first (newest) week expanded, collapse the rest.
watch(() => groupedEntries.value, (newGroups) => {
    if (newGroups && Object.keys(newGroups).length > 0) {
        const weeks = Object.keys(newGroups);
        // We only want to set the defaults once when entries are loaded
        if (weeks.length > 1) {
            collapsedWeeks.value = weeks.slice(1);
        }
    }
}, { immediate: true });

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

const error = ref(null);

const generateReport = () => {
    if (selectedIds.value.length === 0) {
        error.value = 'Please select at least one fragment to weave.';
        return;
    }
    
    // Clear any local error display
    error.value = null;
    
    // Delegate to composable
    weaveFragments(selectedIds.value);
};

</script>

<template>
    <div class="relative group/generator flex flex-col h-full min-h-0">
        <div class="relative p-10 flex flex-col h-full min-h-0 space-y-8">
            <!-- Compact Header -->
            <div class="space-y-1 shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-[1px] bg-[#A68B6A]/30"></div>
                    <p class="text-[9px] uppercase tracking-[0.4em] text-[#A68B6A] font-bold">Divine Synthesis</p>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-[#E3D5C1] font-cinzel tracking-tight">Report Generator</h2>
                <p class="text-[#E3D5C1]/30 text-[11px] font-sans">Weave fragmented memories into a professional chronicle.</p>
            </div>

            <!-- Selection Control -->
            <div class="flex-1 flex flex-col min-h-0 space-y-4">
                <div class="flex justify-between items-center px-1 shrink-0">
                    <label class="text-[10px] uppercase tracking-[0.2em] text-[#A68B6A]/50 font-black font-sans">Archived Fragments</label>
                    <button 
                        @click="toggleAll"
                        class="text-[9px] uppercase tracking-widest text-[#A68B6A] hover:text-[#E3D5C1] font-bold transition-all py-1.5 px-3 bg-white/[0.02] hover:bg-white/[0.05] rounded-full border border-white/[0.05] hover:border-[#A68B6A]/30"
                    >
                        {{ selectedIds.length === availableEntries?.length ? 'Deselect All' : 'Select All' }}
                    </button>
                </div>

                <!-- Scrollable Entries List -->
                <div class="flex-1 overflow-y-auto pr-3 custom-scrollbar min-h-0">
                    <div v-if="!availableEntries || availableEntries.length === 0" class="py-16 text-center">
                        <p class="text-[#A68B6A]/20 text-[10px] uppercase tracking-widest italic font-sans">No fragments found in the vault.</p>
                    </div>
                    
                    <div v-for="(entries, week) in groupedEntries" :key="week" class="mb-8 last:mb-0">
                        <!-- Week Toggle -->
                        <button 
                            @click="toggleWeek(week)" 
                            class="w-full flex items-center gap-3 mb-4 group/week opacity-60 hover:opacity-100 transition-opacity"
                        >
                            <svg 
                                class="w-2.5 h-2.5 text-[#A68B6A] transition-transform duration-300" 
                                :class="{'rotate-[-90deg]': collapsedWeeks.includes(week)}"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                            <h3 class="text-[10px] uppercase tracking-[0.2em] text-[#A68B6A] font-black font-sans group-hover/week:text-[#E3D5C1] transition-colors">{{ week }}</h3>
                            <div class="flex-1 h-[1px] bg-[#A68B6A]/20 group-hover/week:bg-[#A68B6A]/40 transition-colors"></div>
                        </button>

                        <!-- Entries for the week -->
                        <div v-if="!collapsedWeeks.includes(week)" class="space-y-3">
                            <div 
                                v-for="entry in entries" 
                                :key="entry.id"
                                @click="toggleEntry(entry.id)"
                                class="group/entry relative p-5 rounded-2xl border transition-all duration-500 cursor-pointer overflow-hidden mb-4 last:mb-0"
                                :class="selectedIds.includes(entry.id) 
                                    ? 'bg-[#A68B6A]/10 border-[#A68B6A]/40 shadow-[inset_0_0_30px_rgba(166,139,106,0.05)]' 
                                    : 'bg-white/[0.01] border-white/[0.03] hover:border-white/10 hover:bg-white/[0.02]'"
                            >
                                <!-- Highlight Ribbon -->
                                <div class="absolute left-0 top-0 bottom-0 w-1 transition-all duration-500"
                                     :class="selectedIds.includes(entry.id) ? 'bg-[#A68B6A]' : 'bg-transparent'"></div>

                                <div class="relative z-10 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-[#A68B6A]/30 group-hover/entry:bg-[#A68B6A] transition-colors"></div>
                                            <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-[#A68B6A]/60 group-hover/entry:text-[#A68B6A] transition-colors">
                                                {{ entry.title }}
                                            </span>
                                        </div>
                                        <div 
                                            class="w-4 h-4 rounded-full border border-[#A68B6A]/30 flex items-center justify-center transition-all duration-500"
                                            :class="{'bg-[#A68B6A] border-[#A68B6A] shadow-[0_0_15px_rgba(166,139,106,0.5)]': selectedIds.includes(entry.id)}"
                                        >
                                            <svg v-if="selectedIds.includes(entry.id)" class="w-2.5 h-2.5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-[13px] font-sans text-[#E3D5C1]/40 leading-relaxed line-clamp-2 group-hover/entry:text-[#E3D5C1]/70 transition-colors italic">
                                        "{{ entry.content }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 shrink-0 space-y-3">
                <button 
                    @click="generationStep === 4 ? router.visit(route('reports.create', { job_id: currentJobId })) : generateReport()"
                    :disabled="isGenerating || (selectedIds.length === 0 && generationStep !== 4)"
                    class="w-full relative group/btn disabled:opacity-30 transition-all duration-500 rounded-xl overflow-hidden"
                >
                    <div class="absolute inset-0 bg-[#A68B6A] transition-transform duration-500 group-hover/btn:scale-105"
                         :class="{'bg-emerald-600': generationStep === 4}"></div>
                    <div class="relative py-4 px-6 flex items-center justify-center gap-3">
                        <span class="text-[10px] font-cinzel font-bold text-black uppercase tracking-[0.3em] transition-all group-hover/btn:tracking-[0.4em]"
                              :class="{'text-white': generationStep === 4}">
                            {{ isGenerating ? 'Synthesis in Progress...' : (generationStep === 4 ? 'View Chronicle' : `Weave ${selectedIds.length} Fragments`) }}
                        </span>
                        <svg v-if="!isGenerating" class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" 
                             :class="generationStep === 4 ? 'text-white' : 'text-black'"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="generationStep === 4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path v-if="generationStep === 4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        
                        <!-- Mini Spinner -->
                        <div v-if="isGenerating" class="w-4 h-4 rounded-full border-2 border-black/20 border-t-black animate-spin"></div>
                    </div>
                </button>

                <button 
                    @click="router.visit(route('reports.create'))"
                    :disabled="isGenerating"
                    class="w-full py-3.5 px-6 rounded-xl border border-[#A68B6A]/20 hover:border-[#A68B6A]/50 bg-white/[0.02] hover:bg-white/[0.05] text-[#A68B6A] hover:text-[#E3D5C1] transition-all duration-500 flex items-center justify-center gap-3"
                >
                    <span class="text-[9px] uppercase tracking-[0.3em] font-black">Manual Chronicle</span>
                    <svg class="w-3.5 h-3.5 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>

                <p v-if="error" class="mt-3 text-center text-[10px] text-red-400 font-sans tracking-wide uppercase font-bold">{{ error }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.font-cinzel { font-family: 'Cinzel', serif; }

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
