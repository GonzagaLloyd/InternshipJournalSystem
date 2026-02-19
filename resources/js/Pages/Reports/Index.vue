<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, computed } from 'vue';
import ReportGenerator from '@/Components/Reports/ReportGenerator.vue';
import ReportCard from '@/Components/Reports/ReportCard.vue';
import { useTabSync } from '@/Composables/useTabSync';
import { useToast } from '@/Composables/useToast';
import { Head, router } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
import SkeletonLoader from '@/Components/UI/SkeletonLoader.vue';

const props = defineProps({
    availableEntries: Array,
    pastReports: {
        type: Array,
        default: null
    }
});

// Setup tab sync
useTabSync(['pastReports']);

const localPastReports = ref([]);
const showDeleteModal = ref(false);
const reportToDelete = ref(null);

// Sync local state when props arrive
watch(() => props.pastReports, (newVal) => {
    if (newVal) localPastReports.value = [...newVal];
}, { immediate: true });

// Computed to group reports by month
const groupedReports = computed(() => {
    const groups = {};
    if (!localPastReports.value) return groups;

    localPastReports.value.forEach(report => {
        if (!report.created_at) return;
        // created_at format: "Feb 13, 2026 10:41"
        const dateParts = report.created_at.split(' ');
        if (dateParts.length < 3) return;
        
        const month = dateParts[0]; // e.g. "Feb"
        const year = dateParts[2];  // e.g. "2026," (need to remove comma)
        const cleanYear = year.replace(',', '');
        const groupName = `${month} ${cleanYear}`;
        
        if (!groups[groupName]) {
            groups[groupName] = [];
        }
        groups[groupName].push(report);
    });
    return groups;
});

const collapsedMonths = ref([]);

// Initialize: Expand newest month, collapse others
watch(() => groupedReports.value, (newGroups) => {
    const months = Object.keys(newGroups);
    if (months.length > 0 && collapsedMonths.value.length === 0) {
        // Expand the first one (most recent), collapse the rest
        collapsedMonths.value = months.slice(1);
    }
}, { immediate: true });

const toggleMonth = (month) => {
    const index = collapsedMonths.value.indexOf(month);
    if (index > -1) {
        collapsedMonths.value.splice(index, 1);
    } else {
        collapsedMonths.value.push(month);
    }
};

const loadReport = (report) => {
    router.visit(route('reports.show', report.id || report._id));
};

const { success, error: toastError } = useToast();

const isProcessingDelete = ref(false);

const deleteReport = (id) => {
    reportToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    const id = reportToDelete.value;
    if (!id || isProcessingDelete.value) return;

    isProcessingDelete.value = true;
    
    // Tactile feedback pause
    await new Promise(resolve => setTimeout(resolve, 300));
    
    router.delete(route('reports.destroy', id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            localPastReports.value = localPastReports.value.filter(r => r.id !== id);
            success('Chronicle banished to the Sunken Vault.');
            // Broadcast for sync
            const tabSync = useTabSync(['pastReports']);
            tabSync.broadcastUpdate();
            reportToDelete.value = null;
        },
        onError: () => {
            toastError('Banishment failed: The chronicle resists.');
            reportToDelete.value = null;
        },
        onFinish: () => {
            isProcessingDelete.value = false;
        }
    });
};
</script>

<template>
    <Head title="Weekly Reports" />

    <AuthenticatedLayout 
        title="Weekly <span class='text-[#A68B6A]'>Progress</span> Report"
        subtitle="Documentation Library"
    >
        <!-- Locked height on desktop, naturally scrollable on mobile -->
        <div class="xl:h-[calc(100vh-5rem)] xl:overflow-hidden flex flex-col relative font-serif text-[#E3D5C1]">
            
            <!-- Atmospheric background elements -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden opacity-30">
                <div class="absolute -top-[20%] -left-[10%] w-[40%] h-[40%] bg-[#A68B6A]/5 blur-[120px] rounded-full"></div>
                <div class="absolute -bottom-[20%] -right-[10%] w-[40%] h-[40%] bg-[#A68B6A]/5 blur-[120px] rounded-full"></div>
            </div>

            <main class="flex-1 flex flex-col relative z-20 px-4 md:px-8 xl:px-12 py-6 xl:py-10 xl:min-h-0">
                <div class="max-w-[1800px] mx-auto w-full flex-1 flex flex-col xl:min-h-0">
                    
                    <!-- Main Content Grid -->
                    <div class="flex-1 flex flex-col xl:flex-row gap-8 xl:gap-14 relative xl:min-h-0">
                        
                        <!-- Left Column: Generator Pane (Hollowed Glass Design) -->
                        <section class="flex flex-col w-full xl:w-[34rem] 2xl:w-[40rem] shrink-0 relative group/pane xl:min-h-0 min-h-[600px]">
                            <div class="flex-1 flex flex-col bg-[#0D0D0D]/40 backdrop-blur-xl border border-white/[0.04] rounded-[2.5rem] shadow-[inset_0_0_40px_rgba(0,0,0,0.4)] overflow-hidden transition-all duration-1000 group-hover/pane:border-[#A68B6A]/10 group-hover/pane:bg-[#0D0D0D]/50 xl:min-h-0">
                                <div class="flex-1 overflow-y-auto custom-scrollbar">
                                    <ReportGenerator 
                                        :availableEntries="availableEntries" 
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Right Column: Library Pane (Atmospheric & Breathing) -->
                        <section class="flex-1 flex flex-col relative group/pane min-w-0 xl:min-h-0 pb-12 xl:pb-0">
                            <div class="flex-1 flex flex-col bg-transparent overflow-hidden xl:min-h-0">
                                <div class="flex-1 flex flex-col min-h-0 relative">
                                    <!-- Integrated Library Header -->
                                    <div class="px-6 md:px-10 pt-4 md:pt-6 pb-12 shrink-0 flex justify-between items-end">
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#A68B6A]/30 animate-pulse border border-[#A68B6A]/20"></div>
                                                <p class="text-[10px] uppercase tracking-[0.6em] text-[#A68B6A]/60 font-bold font-sans">Archives</p>
                                            </div>
                                            <h2 class="text-3xl md:text-5xl font-serif text-[#E3D5C1] tracking-tight">The <span class="text-[#A68B6A]/70 italic">Report</span> Vault</h2>
                                        </div>
                                        <div class="text-[10px] uppercase tracking-[0.4em] text-[#E3D5C1]/10 font-sans font-black pr-2 mb-2">
                                            {{ localPastReports.length }} Chronicles
                                        </div>
                                    </div>

                                    <!-- Vertical Scrollable Library - Grouped by Month -->
                                    <div class="flex-1 overflow-y-auto custom-scrollbar px-6 md:px-14 pb-20">
                                        <!-- Skeleton Loading State -->
                                        <div v-if="props.pastReports === null" class="max-w-4xl mx-auto space-y-12">
                                            <div v-for="i in 2" :key="i" class="space-y-6">
                                                <SkeletonLoader width="30%" height="1.5rem" />
                                                <div class="grid grid-cols-1 gap-10">
                                                    <div v-for="j in 2" :key="j" class="bg-white/[0.02] border border-white/5 p-8 rounded-xl space-y-4">
                                                        <SkeletonLoader width="20%" height="0.6rem" opacity="0.03" />
                                                        <SkeletonLoader width="70%" height="2rem" />
                                                        <div class="flex gap-4">
                                                            <SkeletonLoader width="40px" height="40px" borderRadius="50%" />
                                                            <SkeletonLoader width="40px" height="40px" borderRadius="50%" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-else-if="localPastReports.length > 0" class="max-w-4xl mx-auto space-y-12">
                                            <div v-for="(reports, month) in groupedReports" :key="month" class="space-y-6">
                                                <!-- Month Header Toggle -->
                                                <button 
                                                    @click="toggleMonth(month)"
                                                    class="w-full flex items-center justify-between group/month cursor-pointer py-4 border-b border-white/5"
                                                >
                                                    <div class="flex items-center gap-4">
                                                        <div class="text-[10px] font-sans font-black tracking-[0.4em] text-[#A68B6A] uppercase flex items-center gap-3">
                                                            <div class="w-2 h-[1px] bg-[#A68B6A]/30"></div>
                                                            {{ month }}
                                                            <span class="text-[8px] opacity-40 lowercase">({{ reports.length }} chronicles)</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="w-6 h-6 rounded-full border border-white/10 flex items-center justify-center transition-all group-hover/month:border-[#A68B6A]/40">
                                                        <svg 
                                                            class="w-3 h-3 text-[#A68B6A]/60 transition-transform duration-500"
                                                            :class="{ 'rotate-180': collapsedMonths.includes(month) }"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </button>

                                                <!-- Month Content -->
                                                <div 
                                                    v-if="!collapsedMonths.includes(month)"
                                                    class="grid grid-cols-1 gap-14 transition-all duration-700 animate-fade-in-down"
                                                >
                                                    <ReportCard 
                                                        v-for="report in reports" 
                                                        :key="report.id" 
                                                        :report="report"
                                                        @click="loadReport"
                                                        @delete="deleteReport"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Atmospheric Empty State -->
                                        <div v-else class="h-full flex flex-col items-center justify-center py-40 text-center space-y-8">
                                            <div class="relative">
                                                <div class="absolute inset-0 bg-[#A68B6A]/5 blur-3xl rounded-full scale-150"></div>
                                                <div class="relative w-28 h-28 rounded-full bg-white/[0.01] border border-white/[0.03] flex items-center justify-center text-[#A68B6A]/20 shadow-[0_0_40px_rgba(0,0,0,0.5)]">
                                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.3" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="space-y-3">
                                                <p class="text-white/10 text-[12px] uppercase tracking-[0.4em] font-sans font-bold italic">
                                                    Silent Archives
                                                </p>
                                                <p class="text-white/5 text-[10px] uppercase tracking-[0.2em] font-sans max-w-xs mx-auto leading-relaxed">
                                                    Generate a chronicle in the generator to populate your vault.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </main>
        </div>

        <!-- Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteModal"
            :processing="isProcessingDelete"
            title="Consign to Vault?"
            message="This chronicle will be moved to the Sunken Vault. It will be hidden from the library but can be recovered from the forgotten depths."
            confirm-text="Banish Record"
            cancel-text="Preserve"
            type="danger"
            @close="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template>

<style scoped>
.font-serif {
    font-family: 'Cormorant Garamond', serif;
}
.font-cinzel {
    font-family: 'Cinzel', serif;
}
/* Use system sans for readable small text */
.font-sans {
    font-family: system-ui, -apple-system, sans-serif;
}

/* Custom Scrollbar for list */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(166, 139, 106, 0.2);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(166, 139, 106, 0.4);
}
</style>
