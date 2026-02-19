<script setup>
import { ref, watch, computed, nextTick } from 'vue';
import { marked } from 'marked';
import { usePage, router } from '@inertiajs/vue3';
import TomeLoader from '@/Components/UI/TomeLoader.vue';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
import { useTabSync } from '@/Composables/useTabSync';
import { cleanUpMarkdown, parseMarkdownToBlocks, blocksToMarkdown } from '@/Utils/MarkdownProcessor';
import { exportToPDF as pdfExporter, exportToDocs as docsExporter } from '@/Utils/ReportExport';
import ReportVisualEditor from './ReportVisualEditor.vue';
import ReportExportView from './ReportExportView.vue';

const props = defineProps({
    report: Object,
    class: String,
    standalone: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['reset', 'saved']);

// Constants & Sessions
const { success, error: toastError } = useToast();
const { broadcastUpdate } = useTabSync([], false);
const page = usePage();

// State
const rawMarkdown = ref('');
const reportTitle = ref('');
const editableUserName = ref('');
const editableUserRole = ref('');
const editableCompanyName = ref('');
const footerText = ref('');
const isExporting = ref(false);
const showArchiveModal = ref(false);
const isEditing = ref(false);
const isSaving = ref(false);
const blocks = ref([]);
const isVisualMode = ref(true);

// Watchers
watch(() => props.report, (newVal) => {
    if (newVal) {
        rawMarkdown.value = newVal.report;
        reportTitle.value = newVal.report_title || 'Weekly Progress Report';
        editableUserName.value = newVal.user_name || page.props.auth.user.name;
        editableUserRole.value = newVal.user_role || "IT Intern";
        editableCompanyName.value = newVal.company_name || "iTech Media Logic";
        footerText.value = newVal.footer_text || "Generated via Internal Journal System";
        isEditing.value = false;
    }
}, { immediate: true });

// Expose internal methods for standalone mode
defineExpose({
    saveReport: () => saveReport(),
    cancelEdit: () => cancelEdit(),
    toggleEdit: () => toggleEdit(),
    exportToPDF: () => exportToPDF(),
    archiveToVault: () => archiveToVault(),
    isEditing,
    isSaving,
    isExporting
});

watch(isEditing, async (val) => {
    if (val && isVisualMode.value) {
        await nextTick();
        // Trigger resize for visibility
        window.dispatchEvent(new Event('resize'));
    }
});

watch(isVisualMode, (val) => {
    if (!val) {
        rawMarkdown.value = blocksToMarkdown(blocks.value);
    } else {
        const cleaned = cleanUpMarkdown(rawMarkdown.value);
        blocks.value = parseMarkdownToBlocks(cleaned);
    }
});

// Computed
const previewContent = computed(() => marked.parse(rawMarkdown.value));

// Actions
const saveReport = async () => {
    isSaving.value = true;
    try {
        if (isVisualMode.value && isEditing.value) {
            rawMarkdown.value = blocksToMarkdown(blocks.value);
        }

        let response;
        const reportId = props.report.id || props.report._id;

        if (reportId) {
             response = await axios.patch(route('reports.update', reportId), {
                report: rawMarkdown.value,
                report_title: reportTitle.value,
                user_name: editableUserName.value,
                user_role: editableUserRole.value,
                company_name: editableCompanyName.value,
                footer_text: footerText.value,
             });
             success('Chronicle updated in the library vault.');
        } else {
             response = await axios.post(route('reports.store'), {
                report: rawMarkdown.value,
                period: props.report.period,
                report_title: reportTitle.value,
                user_name: editableUserName.value,
                user_role: editableUserRole.value,
                company_name: editableCompanyName.value,
                footer_text: footerText.value,
            });
             success('Decree sealed and recorded in the library.');
        }
        
        emit('saved', response.data);
        isEditing.value = false;
    } catch (err) {
        console.error('Failed to save report:', err);
        toastError('Ritual failed: Could not seal the decree.');
    } finally {
        isSaving.value = false;
    }
};

const cancelEdit = () => {
    rawMarkdown.value = props.report.report;
    reportTitle.value = props.report.report_title || 'Weekly Progress Report';
    editableUserName.value = props.report.user_name || page.props.auth.user.name;
    editableUserRole.value = props.report.user_role || "IT Intern";
    editableCompanyName.value = props.report.company_name || "iTech Media Logic";
    footerText.value = props.report.footer_text || "Generated via Internal Journal System";
    isEditing.value = false;
};

const toggleEdit = () => {
    if (!isEditing.value) {
        const cleaned = cleanUpMarkdown(rawMarkdown.value);
        blocks.value = parseMarkdownToBlocks(cleaned);
        isVisualMode.value = true; 
    }
    isEditing.value = !isEditing.value;
};

const confirmArchive = () => {
    const reportId = props.report.id || props.report._id;
    if (reportId) {
        broadcastUpdate();
        router.delete(route('reports.destroy', reportId), {
            onSuccess: () => {
                showArchiveModal.value = false;
                success('Chronicle banished to the Sunken Vault.');
                emit('reset');
            },
            onError: (errors) => {
                console.error('Archive failed:', errors);
                toastError('Banishment failed: The chronicle resists.');
            }
        });
    }
};

const exportToPDF = async () => {
    isExporting.value = true;
    try {
        await pdfExporter({
            element: document.getElementById('report-document'),
            report: {
                ...props.report,
                report_title: reportTitle.value,
                footer_text: footerText.value
            },
            userName: editableUserName.value,
            userRole: editableUserRole.value,
            companyName: editableCompanyName.value
        });
    } catch (error) {
        console.error('PDF Export Error:', error);
        alert('Failed to export PDF.');
    } finally {
        isExporting.value = false;
    }
};

const exportToDocs = () => {
    docsExporter({
        report: {
            ...props.report,
            report: rawMarkdown.value
        },
        userName: editableUserName.value,
        companyName: editableCompanyName.value,
        marked
    });
};

const archiveToVault = () => (showArchiveModal.value = true);

</script>

<template>
    <div :class="['relative flex flex-col h-full group/preview', props.class]">
        <!-- Main Container Background - Only if NOT standalone -->
        <div v-if="!standalone" class="absolute inset-0 bg-[#1A1A1A] rounded-3xl border border-white/[0.05] shadow-2xl"></div>
        
        <div :class="['relative h-full flex flex-col', standalone ? 'p-0' : 'p-4 sm:p-6 md:p-10']">
            <!-- Header Status Section - Only if NOT standalone -->
            <div v-if="!standalone" class="flex flex-col gap-4 mb-6 shrink-0">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-center sm:text-left">
                    <div class="w-full sm:w-auto">
                        <p class="text-[8px] sm:text-[10px] uppercase tracking-[0.2em] text-[#A68B6A] font-bold">Generated Documentation</p>
                        <h2 class="text-xl md:text-3xl font-bold text-[#E3D5C1] font-cinzel">Weekly Progress Report</h2>
                        <div class="flex items-center justify-center sm:justify-start gap-2 sm:gap-3 mt-1">
                            <p class="text-[#A68B6A]/60 text-[8px] sm:text-[10px] uppercase tracking-widest font-bold">
                                {{ report.period?.start || 'N/A' }} — {{ report.period?.end || 'N/A' }}
                            </p>
                            <div class="h-1 w-1 rounded-full bg-[#A68B6A]/30"></div>
                            <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-[#A68B6A]/40 font-bold">Ready</span>
                        </div>
                    </div>
                </div>

                <!-- Action Toolbar -->
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 w-full">
                    <template v-if="!isEditing">
                        <button v-if="report.id || report._id" @click="archiveToVault" class="action-btn-secondary flex-1 sm:flex-none py-2 px-4">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                            <span class="text-[9px] whitespace-nowrap">Vault</span>
                        </button>
                        
                        <button @click="toggleEdit" class="action-btn-secondary flex-1 sm:flex-none py-2 px-4">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            <span class="text-[9px] whitespace-nowrap">Amend</span>
                        </button>

                        <button v-if="!report.id && !report._id" @click="saveReport" :disabled="isSaving" class="action-btn-primary flex-1 sm:flex-none py-2 px-6">
                            <svg v-if="!isSaving" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            <svg v-else class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-[9px] whitespace-nowrap">{{ isSaving ? 'Saving...' : 'Save Report' }}</span>
                        </button>

                        <button v-if="report.id || report._id" @click="exportToPDF" :disabled="isExporting" class="action-btn-primary flex-1 sm:flex-none py-2 px-6">
                            <svg v-if="!isExporting" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <svg v-else class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-[9px] whitespace-nowrap">{{ isExporting ? 'Exporting...' : 'Export PDF' }}</span>
                        </button>
                    </template>

                    <!-- Edit Mode Toolbar -->
                    <template v-else>
                         <button @click="cancelEdit" class="action-btn-secondary flex-1 sm:flex-none py-2 px-4">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            <span class="text-[9px] whitespace-nowrap">Cancel</span>
                        </button>
                        
                        <button @click="saveReport" :disabled="isSaving" class="action-btn-primary flex-1 sm:flex-none py-2 px-6">
                            <svg v-if="!isSaving" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            <svg v-else class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-[9px] whitespace-nowrap">{{ isSaving ? 'Saving...' : 'Save Changes' }}</span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Content Area -->
            <div :class="['flex-1 overflow-y-auto px-1 custom-scrollbar', standalone ? 'pb-20' : '']">
                <!-- PDF Capture View (Hidden) -->
                <ReportExportView 
                    :content="rawMarkdown"
                    :companyName="editableCompanyName"
                    :report="{ ...report, report_title: reportTitle, footer_text: footerText }"
                    :userName="editableUserName"
                    :userRole="editableUserRole"
                />

                <!-- Live Preview View -->
                <div v-if="!isEditing" class="pb-10 pt-4 md:pt-10">
                    <div class="mb-8 md:mb-16 pb-8 md:pb-12 border-b border-white/5 flex flex-col items-center">
                        <!-- Centered Cinematic Header (Matching Entries/Show) -->
                        <div class="h-8 md:h-16 w-[1px] bg-gradient-to-b from-transparent via-[#A68B6A]/30 to-transparent mb-6 md:mb-8"></div>
                        
                        <div class="text-[8px] sm:text-[10px] md:text-xs font-cinzel tracking-[0.2em] md:tracking-[0.4em] text-[#A68B6A] uppercase bg-[#A68B6A]/5 px-4 md:px-8 py-1.5 md:py-2 rounded-full border border-[#A68B6A]/10 transition-all whitespace-nowrap mb-6 md:mb-10">
                             Period: {{ report.period?.start }} — {{ report.period?.end }}
                        </div>

                        <h1 class="text-2xl sm:text-3xl md:text-5xl lg:text-7xl font-cinzel font-bold text-[#E3D5C1] leading-tight tracking-tight text-center max-w-4xl mx-auto px-4">
                            {{ reportTitle }}
                        </h1>

                        <div class="mt-12 flex flex-wrap justify-center gap-12 text-center">
                            <div class="space-y-1">
                                <p class="text-[9px] uppercase tracking-[0.3em] text-[#A68B6A] font-bold">Intern</p>
                                <p class="text-sm text-[#E3D5C1]/70 font-serif italic">{{ editableUserName }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] uppercase tracking-[0.3em] text-[#A68B6A] font-bold">Role</p>
                                <p class="text-sm text-[#E3D5C1]/70 font-serif italic">{{ editableUserRole }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] uppercase tracking-[0.3em] text-[#A68B6A] font-bold">Company</p>
                                <p class="text-sm text-[#E3D5C1]/70 font-serif italic">{{ editableCompanyName }}</p>
                            </div>
                        </div>

                        <div class="mt-12 h-[1px] w-24 bg-gradient-to-r from-transparent via-[#A68B6A]/40 to-transparent"></div>
                    </div>

                    <div class="preview-markdown-content prose prose-invert max-w-none" v-html="previewContent"></div>
                </div>

                <!-- Editor View -->
                <div v-else class="space-y-12 pb-20">
                    <!-- Header Editor -->
                    <div class="bg-white/[0.02] border border-white/[0.05] rounded-3xl p-8 sm:p-10 space-y-8">
                        <div class="flex items-center gap-3 mb-2">
                             <div class="w-1 h-1 rounded-full bg-[#A68B6A]"></div>
                             <p class="text-[10px] uppercase tracking-[0.2em] text-[#A68B6A] font-bold underline decoration-[#A68B6A]/30 underline-offset-8">Header Metadata</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[9px] uppercase tracking-[0.2em] text-[#A68B6A]/60 font-bold ml-1">Report Title</label>
                                <input v-model="reportTitle" type="text" class="w-full bg-[#1A1A1A]/60 border border-white/5 rounded-xl px-5 py-4 text-[#E3D5C1] font-cinzel text-lg focus:border-[#A68B6A]/30 focus:ring-0 transition-all" />
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[9px] uppercase tracking-[0.2em] text-[#A68B6A]/60 font-bold ml-1">Intern Name</label>
                                <input v-model="editableUserName" type="text" class="w-full bg-[#1A1A1A]/60 border border-white/5 rounded-xl px-5 py-4 text-[#E3D5C1] font-serif italic focus:border-[#A68B6A]/30 focus:ring-0 transition-all" />
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[9px] uppercase tracking-[0.2em] text-[#A68B6A]/60 font-bold ml-1">Role / Designation</label>
                                <input v-model="editableUserRole" type="text" class="w-full bg-[#1A1A1A]/60 border border-white/5 rounded-xl px-5 py-4 text-[#E3D5C1] font-serif italic focus:border-[#A68B6A]/30 focus:ring-0 transition-all" />
                            </div>
                            
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[9px] uppercase tracking-[0.2em] text-[#A68B6A]/60 font-bold ml-1">Company Name</label>
                                <input v-model="editableCompanyName" type="text" class="w-full bg-[#1A1A1A]/60 border border-white/5 rounded-xl px-5 py-4 text-[#E3D5C1] font-serif italic focus:border-[#A68B6A]/30 focus:ring-0 transition-all" />
                            </div>
                        </div>
                    </div>

                    <!-- Content Editor -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between px-1">
                             <div class="flex items-center gap-3">
                                 <div class="w-1 h-1 rounded-full bg-[#A68B6A]"></div>
                                 <p class="text-[10px] uppercase tracking-[0.2em] text-[#A68B6A] font-bold">Chronicle Content</p>
                             </div>
                             <button type="button" @click="isVisualMode = !isVisualMode" class="toggle-mode-btn">
                                Switch to {{ isVisualMode ? 'Raw' : 'Visual' }}
                             </button>
                        </div>

                        <ReportVisualEditor 
                            v-model:blocks="blocks"
                            v-model:rawMarkdown="rawMarkdown"
                            :isVisualMode="isVisualMode"
                            standalone
                        />
                    </div>

                    <!-- Footer Editor -->
                    <div class="bg-white/[0.02] border border-white/[0.05] rounded-3xl p-8 sm:p-10 space-y-4">
                        <div class="flex items-center gap-3 mb-2">
                             <div class="w-1 h-1 rounded-full bg-[#A68B6A]"></div>
                             <p class="text-[10px] uppercase tracking-[0.2em] text-[#A68B6A] font-bold underline decoration-[#A68B6A]/30 underline-offset-8">Footer Attribution</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] uppercase tracking-[0.2em] text-[#A68B6A]/60 font-bold ml-1">Footer Text</label>
                            <input v-model="footerText" type="text" class="w-full bg-[#1A1A1A]/60 border border-white/5 rounded-xl px-5 py-4 text-[#E3D5C1] font-serif italic focus:border-[#A68B6A]/30 focus:ring-0 transition-all" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <ConfirmationModal 
        :show="showArchiveModal"
        title="Consign to the Vault?"
        message="This report shall be cast into the Sunken Vault, where forgotten chronicles rest."
        confirm-text="Cast into Vault"
        cancel-text="Keep in Archives"
        type="warning"
        @close="showArchiveModal = false"
        @confirm="confirmArchive"
    />
</template>

<style scoped>
/* Utility Button Classes */
.action-btn-primary {
    @apply h-12 bg-[#A68B6A] hover:bg-[#C9B79C] text-[#1B1B1B] rounded-xl transition-all shadow-xl flex items-center justify-center gap-2 text-[10px] uppercase tracking-widest font-black disabled:opacity-50;
}
.action-btn-secondary {
    @apply h-12 bg-white/[0.04] hover:bg-white/[0.1] text-[#E3D5C1] rounded-xl border border-white/[0.08] transition-all flex items-center justify-center gap-2 text-[10px] uppercase tracking-widest font-bold;
}
.toggle-mode-btn {
    @apply text-[9px] uppercase tracking-widest text-[#A68B6A]/60 hover:text-[#A68B6A] font-bold transition-all px-2 py-1 bg-white/[0.03] border border-white/5 rounded;
}

.scrollbar-style::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-style::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.02);
}
.scrollbar-style::-webkit-scrollbar-thumb {
    background: rgba(166, 139, 106, 0.3);
    border-radius: 3px;
}
.scrollbar-style::-webkit-scrollbar-thumb:hover {
    background: rgba(166, 139, 106, 0.5);
}

/* Preview Content Styling */
:deep(.preview-markdown-content h1) { 
    font-family: 'Cinzel', serif; 
    letter-spacing: 0.1em; 
    color: #E3D5C1;
    text-align: center;
    font-size: 2.25rem;
    margin-bottom: 2rem;
}

:deep(.preview-markdown-content h2) { 
    font-family: 'Cinzel', serif; 
    color: #A68B6A;
    font-size: 1.5rem;
    letter-spacing: 0.05em; 
    border-left: 2px solid #A68B6A; 
    padding-left: 1.5rem; 
    margin-top: 3rem;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
}

:deep(.preview-markdown-content h3) { 
    color: #E3D5C1; 
    font-weight: bold; 
    margin-top: 2rem; 
    margin-bottom: 1rem; 
    font-size: 1.1rem;
    letter-spacing: 0.05em;
}

:deep(.preview-markdown-content p) {
    color: rgba(227, 213, 193, 0.85);
    line-height: 1.8;
    font-size: 1.125rem;
    margin-bottom: 1.5rem;
    text-align: justify;
}

:deep(.preview-markdown-content p:first-of-type::first-letter) {
    font-family: 'Cinzel', serif;
    font-size: 3.5rem;
    color: #A68B6A;
    margin-right: 0.75rem;
    float: left;
    line-height: 0.8;
    margin-top: 0.25rem;
}

:deep(.preview-markdown-content ul) {
    list-style-type: none;
    padding-left: 1.5rem;
    margin-bottom: 2rem;
}

:deep(.preview-markdown-content li) {
    color: rgba(227, 213, 193, 0.8);
    font-size: 1.1rem;
    position: relative;
    margin-bottom: 0.75rem;
}

:deep(.preview-markdown-content li::before) {
    content: "•";
    color: #A68B6A;
    position: absolute;
    left: -1.5rem;
    font-weight: bold;
}

:deep(.preview-markdown-content strong) { 
    color: #A68B6A; 
}

:deep(.preview-markdown-content em) {
    color: rgba(227, 213, 193, 0.6);
    font-style: italic;
}
</style>
