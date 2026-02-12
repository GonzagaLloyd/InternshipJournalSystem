<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import ReportGenerator from '@/Components/Reports/ReportGenerator.vue';
import ReportPreview from '@/Components/Reports/ReportPreview.vue';

const props = defineProps({
    availableEntries: Array
});

const generatedReport = ref(null);

const handleReportGenerated = (data) => {
    generatedReport.value = data;
};

const resetReport = () => {
    generatedReport.value = null;
};
</script>

<template>
    <Head title="Weekly Chronicle - Grand Archives" />

    <AuthenticatedLayout 
        title="The <span class='text-[#A68B6A]'>Grand</span> Archives"
        subtitle="Weekly Chronicle Generation"
    >
        <div class="xl:h-[calc(100vh-5rem)] xl:overflow-hidden flex flex-col relative font-serif text-[#E3D5C1]">
            <main class="flex-1 flex flex-col relative z-20 px-4 md:px-8 xl:px-12 py-6 xl:py-8 xl:min-h-0">
                <div class="max-w-[1400px] mx-auto w-full flex-1 flex flex-col xl:min-h-0">
                    
                    <div class="flex-1 flex flex-col xl:flex-row gap-8 xl:gap-12 relative xl:min-h-0">
                        <!-- Left Pillar: Generator -->
                        <div class="w-full xl:w-[28rem] shrink-0">
                            <ReportGenerator 
                                :availableEntries="availableEntries" 
                                @generated="handleReportGenerated" 
                            />
                            
                            <div class="mt-8 p-6 rounded-2xl border border-white/[0.03] bg-white/[0.01]">
                                <h3 class="text-[#A68B6A] uppercase tracking-[0.3em] text-[10px] font-bold mb-3">Scribe's Guidance</h3>
                                <ul class="space-y-3 text-[11px] text-[#E3D5C1]/40 italic leading-relaxed">
                                    <li>• Ensure you have inscribed at least three entries for the week for a meaningful chronicle.</li>
                                    <li>• The AI will weave your fragmented thoughts into a cohesive narrative of growth.</li>
                                    <li>• Exported parchments (PDF) are formatted for the finest royal libraries.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Right Pillar: Preview -->
                        <div class="flex-1 flex flex-col relative min-h-[500px] xl:min-h-0">
                            <!-- Empty State -->
                            <div v-if="!generatedReport" class="flex-1 flex flex-col items-center justify-center border border-dashed border-white/10 rounded-3xl bg-white/[0.02] p-12 text-center">
                                <div class="w-20 h-20 mb-6 text-[#A68B6A]/20">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-cinzel text-[#E3D5C1]/60 mb-2">The Archives are Void</h3>
                                <p class="text-[#A68B6A]/40 text-sm max-w-xs mx-auto italic">Summon a report from the left pillar to witness the weaving of your chronicle.</p>
                            </div>

                            <!-- Preview Component -->
                            <ReportPreview 
                                v-else 
                                :report="generatedReport" 
                                @reset="resetReport"
                            />

                            <!-- Decorative Corners -->
                            <div v-if="generatedReport" class="absolute -top-1 -left-1 w-8 h-8 border-t-2 border-l-2 border-[#A68B6A]/30 rounded-tl-xl pointer-events-none"></div>
                            <div v-if="generatedReport" class="absolute -top-1 -right-1 w-8 h-8 border-t-2 border-r-2 border-[#A68B6A]/30 rounded-tr-xl pointer-events-none"></div>
                            <div v-if="generatedReport" class="absolute -bottom-1 -left-1 w-8 h-8 border-b-2 border-l-2 border-[#A68B6A]/30 rounded-bl-xl pointer-events-none"></div>
                            <div v-if="generatedReport" class="absolute -bottom-1 -right-1 w-8 h-8 border-b-2 border-r-2 border-[#A68B6A]/30 rounded-br-xl pointer-events-none"></div>
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
</style>
