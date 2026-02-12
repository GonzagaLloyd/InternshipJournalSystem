<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import ReportGenerator from '@/Components/Reports/ReportGenerator.vue';
import ReportPreview from '@/Components/Reports/ReportPreview.vue';

const props = defineProps({
    availableEntries: Array,
    pastReports: Array
});

const generatedReport = ref(null);

const handleReportGenerated = (data) => {
    generatedReport.value = data;
};

const loadReport = (report) => {
    generatedReport.value = {
        id: report.id,
        _id: report._id,
        report: report.report,
        period: report.period
    };
};

const resetReport = () => {
    generatedReport.value = null;
};
</script>

<template>
    <Head title="Weekly Reports" />

    <AuthenticatedLayout 
        title="Weekly <span class='text-[#A68B6A]'>Progress</span> Report"
        subtitle="Documentation Generation"
    >
        <div class="flex-1 lg:h-[calc(100vh-5rem)] lg:overflow-hidden flex flex-col relative font-serif text-[#E3D5C1]">
            <main class="flex-1 flex flex-col relative z-20 px-4 md:px-8 lg:px-12 pt-6 pb-6 lg:pb-12 min-h-0">
                <div class="max-w-[1600px] mx-auto w-full flex-1 flex flex-col min-h-0">
                    
                    <div class="flex-1 flex flex-col lg:flex-row gap-8 relative min-h-0">
                        <!-- Left Panel: Generator Controls -->
                        <div class="w-full lg:w-[28rem] shrink-0 flex flex-col gap-6 relative min-h-0 overflow-y-auto pr-2 custom-scrollbar">
                            <ReportGenerator 
                                :availableEntries="availableEntries" 
                                @generated="handleReportGenerated" 
                            />
                            
                            <!-- Past Reports Section -->
                            <div class="p-6 rounded-2xl border border-white/[0.03] bg-white/[0.01]">
                                <h3 class="text-[#A68B6A] uppercase tracking-[0.2em] text-[10px] font-bold mb-4 font-sans flex items-center gap-2">
                                    <svg class="w-3 h-3 text-[#A68B6A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Archived Reports
                                </h3>
                                
                                <div v-if="pastReports && pastReports.length > 0" class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                                    <button 
                                        v-for="report in pastReports" 
                                        :key="report.id"
                                        @click="loadReport(report)"
                                        class="w-full text-left p-3 rounded-lg bg-white/[0.03] hover:bg-white/[0.06] border border-white/5 hover:border-[#A68B6A]/30 transition-all group"
                                    >
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-[11px] text-[#E3D5C1] font-sans font-medium group-hover:text-[#A68B6A] transition-colors">
                                                {{ report.period?.start || 'N/A' }} - {{ report.period?.end || 'N/A' }}
                                            </span>
                                            <span class="text-[9px] text-white/30 font-sans">{{ report.created_at }}</span>
                                        </div>
                                    </button>
                                </div>
                                <div v-else class="text-xs text-white/30 italic text-center py-4 font-sans">
                                    No past reports found.
                                </div>
                            </div>

                            <div class="p-6 rounded-2xl border border-white/[0.03] bg-white/[0.01]">
                                <h3 class="text-[#A68B6A] uppercase tracking-[0.2em] text-[10px] font-bold mb-3 font-sans">Documentation Guidelines</h3>
                                <ul class="space-y-3 text-xs text-[#E3D5C1]/50 leading-relaxed font-sans">
                                    <li>• Select at least 3 daily logs for a comprehensive weekly report.</li>
                                    <li>• The system will compile your entries into a professional technical format.</li>
                                    <li>• Generated reports are ready for PDF export and submission.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Right Panel: Preview & Export -->
                        <div class="flex-1 flex flex-col relative min-w-0 min-h-[500px] lg:min-h-0">
                            <!-- Empty State -->
                            <div v-if="!generatedReport" class="flex-1 flex flex-col items-center justify-center border border-dashed border-white/10 rounded-3xl bg-white/[0.02] p-12 text-center">
                                <div class="w-20 h-20 mb-6 text-[#A68B6A]/20">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-cinzel text-[#E3D5C1]/60 mb-2">Report Area Empty</h3>
                                <p class="text-[#A68B6A]/40 text-sm max-w-xs mx-auto font-sans">
                                    Select entries to generate a new report, or choose an archived report from the list.
                                </p>
                            </div>

                            <!-- Preview Component (Ensure it takes full space to enable its internal scroll) -->
                            <ReportPreview 
                                v-else 
                                :report="generatedReport" 
                                @reset="resetReport"
                                class="flex-1 min-h-0"
                            />

                            <!-- Decorative Corners (Subtle, anchored to the boundaries) -->
                            <div v-if="generatedReport" class="absolute -top-1 -left-1 w-6 h-6 border-t border-l border-[#A68B6A]/20 rounded-tl-lg pointer-events-none"></div>
                            <div v-if="generatedReport" class="absolute -top-1 -right-1 w-6 h-6 border-t border-r border-[#A68B6A]/20 rounded-tr-lg pointer-events-none"></div>
                            <div v-if="generatedReport" class="absolute -bottom-1 -left-1 w-6 h-6 border-b border-l border-[#A68B6A]/20 rounded-bl-lg pointer-events-none"></div>
                            <div v-if="generatedReport" class="absolute -bottom-1 -right-1 w-6 h-6 border-b border-r border-[#A68B6A]/20 rounded-br-lg pointer-events-none"></div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
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
