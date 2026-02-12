<script setup>
import { ref, watch, onMounted } from 'vue';
import { marked } from 'marked';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

const props = defineProps({
    report: Object
});

const reportContent = ref('');
const isExporting = ref(false);

watch(() => props.report, (newVal) => {
    if (newVal) {
        reportContent.value = marked.parse(newVal.report);
    }
}, { immediate: true });

const exportToPDF = async () => {
    isExporting.value = true;
    const element = document.getElementById('report-document');
    
    try {
        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#F4E7D3', // Parchment-like background for PDF
        });
        
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgWidth = 210;
        const pageHeight = 297;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save(`Weekly-Chronicle-${props.report.period.start.replace(/ /g, '-')}.pdf`);
    } catch (error) {
        console.error('PDF Export failed:', error);
    } finally {
        isExporting.value = false;
    }
};

const exportToDocs = () => {
    const content = props.report.report;
    const blob = new Blob(['\ufeff', content], {
        type: 'application/msword'
    });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `Weekly-Chronicle-${props.report.period.start.replace(/ /g, '-')}.doc`;
    link.click();
    URL.revokeObjectURL(url);
};
</script>

<template>
    <div class="relative flex flex-col h-full overflow-hidden group/preview">
        <!-- Main Container -->
        <div class="absolute inset-0 bg-[#1A1A1A] rounded-3xl border border-white/[0.05] shadow-2xl"></div>
        
        <div class="relative h-full flex flex-col p-6 md:p-10">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 shrink-0">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.5em] text-[#A68B6A] font-bold">Grand Archival Result</p>
                    <h2 class="text-2xl md:text-3xl font-bold text-[#E3D5C1] font-cinzel">The Weekly Chronicle</h2>
                    <p class="text-[#A68B6A]/60 text-[10px] uppercase tracking-widest mt-1 font-bold">
                        {{ report.period.start }} — {{ report.period.end }}
                    </p>
                </div>

                <div class="flex items-center gap-4 w-full md:w-auto">
                    <button 
                        @click="exportToDocs"
                        class="flex-1 md:flex-none h-11 bg-white/[0.04] hover:bg-white/[0.1] text-[#E3D5C1] px-6 rounded-xl border border-white/[0.08] transition-all flex items-center justify-center gap-2 text-[10px] uppercase tracking-widest font-bold"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export DOCS
                    </button>
                    <button 
                        @click="exportToPDF"
                        :disabled="isExporting"
                        class="flex-1 md:flex-none h-11 bg-[#A68B6A] hover:bg-[#C9B79C] text-[#1B1B1B] px-6 rounded-xl transition-all shadow-xl flex items-center justify-center gap-2 text-[10px] uppercase tracking-widest font-black disabled:opacity-50"
                    >
                        <svg v-if="!isExporting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        <svg v-else class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ isExporting ? 'Preparing...' : 'Export PDF' }}
                    </button>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto pr-2 scrollbar-style">
                <!-- Hidden Actual Export View (to styling for PDF) -->
                <div id="report-document" class="hidden-for-export print-view">
                    <div class="p-12 bg-[#F4E7D3] text-[#2C241A] font-serif min-h-screen border-[16px] border-[#D4C3A3]">
                        <div class="border-2 border-[#A68B6A]/30 p-8 min-h-full">
                            <header class="text-center mb-12 border-b border-[#A68B6A]/20 pb-10">
                                <p class="text-[10px] uppercase tracking-[0.5em] text-[#A68B6A] font-bold mb-3">Office of the Grand Scribe</p>
                                <h1 class="text-4xl font-bold font-cinzel mb-2">WEEKLY CHRONICLE</h1>
                                <p class="text-sm italic text-[#A68B6A]">{{ report.period.start }} — {{ report.period.end }}</p>
                            </header>
                            <div class="prose prose-sepia max-w-none" v-html="reportContent"></div>
                            <footer class="mt-20 pt-10 border-t border-[#A68B6A]/10 text-center">
                                <p class="text-[10px] uppercase tracking-widest text-[#A68B6A]/50">Sealed by the Divine Muse</p>
                            </footer>
                        </div>
                    </div>
                </div>

                <!-- Live Preview View -->
                <div class="prose prose-invert max-w-none prose-p:text-[#E3D5C1]/70 prose-headings:text-[#E3D5C1] prose-headings:font-cinzel prose-strong:text-[#A68B6A] prose-hr:border-white/5" v-html="reportContent"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-style::-webkit-scrollbar {
    width: 4px;
}
.scrollbar-style::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-style::-webkit-scrollbar-thumb {
    background: rgba(166, 139, 106, 0.2);
    border-radius: 2px;
}
.scrollbar-style::-webkit-scrollbar-thumb:hover {
    background: rgba(166, 139, 106, 0.4);
}

/* Print Styling */
.print-view {
    position: fixed;
    top: 9999px;
    left: 9999px;
    width: 800px; /* Standard A4 ratio approximate */
}

:deep(.prose h1) { font-size: 1.875rem; margin-top: 2rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(166, 139, 106, 0.2); padding-bottom: 0.5rem; }
:deep(.prose h2) { font-size: 1.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #A68B6A; }
:deep(.prose h3) { font-size: 1.25rem; margin-top: 1.25rem; margin-bottom: 0.5rem; }
:deep(.prose p) { line-height: 1.8; margin-bottom: 1.25rem; }
:deep(.prose ul) { margin-bottom: 1.25rem; list-style-type: square; padding-left: 1.25rem; }
:deep(.prose li) { margin-bottom: 0.5rem; color: rgba(227, 213, 193, 0.8); }
</style>
