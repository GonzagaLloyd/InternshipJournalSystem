<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReportPreview from '@/Components/Reports/ReportPreview.vue';

import SkeletonLoader from '@/Components/UI/SkeletonLoader.vue';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
import Toast from '@/Components/UI/Toast.vue';
import ReportProgressWidget from '@/Components/UI/ReportProgressWidget.vue';

const props = defineProps({
    report: {
        type: Object,
        default: null
    }
});

const previewRef = ref(null);
const showDeleteModal = ref(false);
const isScrolled = ref(false);

// Helper to access internal state of ReportPreview
const isEditing = computed(() => previewRef.value?.isEditing || false);
const isSaving = computed(() => previewRef.value?.isSaving || false);
const isExporting = computed(() => previewRef.value?.isExporting || false);

const handleReturn = () => {
    router.visit(route('reports.index'));
};

const handleEdit = () => previewRef.value?.toggleEdit();
const handleSave = () => previewRef.value?.saveReport();
const handleCancel = () => previewRef.value?.cancelEdit();
const handleExport = () => previewRef.value?.exportToPDF();
const handleArchive = () => (showDeleteModal.value = true);

const isProcessingDelete = ref(false);

const confirmArchive = async () => {
    const reportId = props.report.id || props.report._id;
    if (reportId && !isProcessingDelete.value) {
        isProcessingDelete.value = true;
        
        // Tactile feedback
        await new Promise(resolve => setTimeout(resolve, 300));
        
        router.delete(route('reports.destroy', reportId), {
            onFinish: () => {
                isProcessingDelete.value = false;
                showDeleteModal.value = false;
            }
        });
    }
};

const handleReset = () => handleReturn();
const handleSaved = (savedReport) => console.log('Report saved');

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

</script>

<template>
    <Head title="Weekly Report" />

    <div class="text-[#C9B79C] py-8 md:py-12 lg:py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center relative font-serif min-h-screen bg-[#1B1B1B]">
        
        <!-- Aligned Atmosphere -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-[40rem] h-[40rem] bg-[#8C6A4A]/5 blur-[120px] rounded-full"></div>
            <div class="absolute inset-0 opacity-[0.1] bg-[url('https://www.transparenttextures.com/patterns/dust.png')]"></div>
        </div>



        <!-- Header Navigation & Actions (High Performance Fixed Header) -->
        <div class="sticky top-0 w-full z-40 flex justify-center h-24 pointer-events-none">
            <!-- Smooth Background Layer (Pure GPU - No Layout Shift) -->
            <div 
                :class="[
                    'absolute inset-0 transition-[opacity,transform] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu will-change-[opacity,transform]',
                    isScrolled 
                        ? 'opacity-100 translate-y-0 bg-[#1B1B1B]/80 backdrop-blur-md border-b border-white/[0.08] shadow-[0_10px_30px_-10px_rgba(0,0,0,0.5)]' 
                        : 'opacity-0 -translate-y-full'
                ]"
                style="height: 5rem;"
            ></div>

            <div v-if="props.report" :class="[
                'relative w-full max-w-4xl flex items-center justify-between gap-4 px-4 sm:px-6 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu pointer-events-auto h-full', 
                isScrolled ? 'md:max-w-5xl translate-y-[-0.5rem] scale-[0.95]' : 'translate-y-0 scale-100'
            ]">
                <!-- Return Link -->
                <a href="#" @click.prevent="handleReturn" class="inline-flex items-center gap-3 text-[#8C6A4A]/60 hover:text-[#C9B79C] transition-colors duration-300 group">
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-full border border-[#8C6A4A]/20 flex items-center justify-center group-hover:border-[#8C6A4A]/60 transition-all duration-300 bg-[#8C6A4A]/5">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <div class="hidden sm:block overflow-hidden h-4 relative w-32">
                        <Transition name="fade-slide">
                            <span v-if="!isScrolled" key="full" class="absolute inset-0 text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">Return to Ledger</span>
                            <span v-else key="short" class="absolute inset-0 text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">Archives</span>
                        </Transition>
                    </div>
                </a>

                <!-- Action Group -->
                <div class="flex items-center gap-2 sm:gap-4 md:gap-6">
                    <template v-if="!isEditing">
                        <!-- Export PDF -->
                        <button @click="handleExport" :disabled="isExporting" class="action-trigger-btn group" title="Export PDF">
                            <div class="hidden md:block overflow-hidden h-3 relative w-20 text-right">
                                <Transition name="fade-slide">
                                    <span v-if="!isScrolled" key="full-pdf" class="absolute inset-0 text-[9px] font-cinzel tracking-[0.15em] uppercase text-[#C9B79C]/60 group-hover:text-[#C9B79C] transition-colors">Export PDF</span>
                                    <span v-else key="short-pdf" class="absolute inset-0 text-[9px] font-cinzel tracking-[0.15em] uppercase text-[#C9B79C]/60 group-hover:text-[#C9B79C] transition-colors">PDF</span>
                                </Transition>
                            </div>
                            <div class="action-icon-circle border-[#C9B79C]/10 bg-[#C9B79C]/5 group-hover:border-[#C9B79C]/40 group-hover:bg-[#C9B79C]/10">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            </div>
                        </button>

                        <!-- Strike Record -->
                        <button @click="handleArchive" class="action-trigger-btn group" title="Strike Record">
                            <div class="hidden md:block overflow-hidden h-3 relative w-32 text-right">
                                <Transition name="fade-slide">
                                    <span v-if="!isScrolled" key="full-strike" class="absolute inset-0 text-[9px] font-cinzel tracking-[0.15em] uppercase text-red-500/40 group-hover:text-red-500 transition-colors">Strike Record</span>
                                    <span v-else key="short-strike" class="absolute inset-0 text-[9px] font-cinzel tracking-[0.15em] uppercase text-red-500/40 group-hover:text-red-500 transition-colors">Strike</span>
                                </Transition>
                            </div>
                            <div class="action-icon-circle border-red-500/10 bg-red-500/5 group-hover:border-red-500/40 group-hover:bg-red-500/10">
                                <svg class="w-3.5 h-3.5 text-red-500/60 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </div>
                        </button>

                        <!-- Amend Report -->
                        <button @click="handleEdit" class="action-trigger-btn group" title="Amend Report">
                            <div class="hidden md:block overflow-hidden h-3 relative w-32 text-right">
                                <Transition name="fade-slide">
                                    <span v-if="!isScrolled" key="full-amend" class="absolute inset-0 text-[9px] font-cinzel tracking-[0.15em] uppercase text-[#A68B6A] group-hover:text-[#C9B79C] transition-colors">Amend Lore</span>
                                    <span v-else key="short-amend" class="absolute inset-0 text-[9px] font-cinzel tracking-[0.15em] uppercase text-[#A68B6A] group-hover:text-[#C9B79C] transition-colors">Amend</span>
                                </Transition>
                            </div>
                            <div class="action-icon-circle border-[#A68B6A]/30 bg-[#A68B6A]/5 group-hover:border-[#A68B6A] group-hover:bg-[#A68B6A]/10">
                                <svg class="w-3.5 h-3.5 text-[#A68B6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </div>
                        </button>
                    </template>

                    <template v-else>
                        <!-- Edit Mode Actions -->
                        <button @click="handleCancel" class="text-[10px] font-cinzel tracking-widest uppercase text-[#8C6A4A]/60 hover:text-[#C9B79C] px-2 py-2 transition-colors">
                            Discard
                        </button>
                        
                        <button @click="handleSave" :disabled="isSaving" class="inline-flex items-center gap-2 group transition-all duration-300">
                            <span class="hidden sm:inline text-[10px] font-cinzel tracking-[0.2em] uppercase text-[#C9B79C]/80 group-hover:text-white">{{ isSaving ? 'Sealing...' : 'Seal Decree' }}</span>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-full border border-[#C9B79C]/30 flex items-center justify-center group-hover:border-[#C9B79C] transition-all bg-[#C9B79C]/10 shadow-[0_0_20px_rgba(201,183,156,0.1)] group-hover:shadow-[0_0_30px_rgba(201,183,156,0.2)]">
                                <svg v-if="!isSaving" class="w-4 h-4 text-[#C9B79C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <svg v-else class="animate-spin w-4 h-4 text-[#C9B79C]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Main Report Content -->
        <article class="w-full max-w-4xl relative z-30 bg-[#2D2D2D]/40 backdrop-blur-[2px] border border-white/5 p-6 sm:p-10 md:p-16 rounded-sm shadow-2xl animate-fade-in-up">
            <!-- Ornamental Border Corners -->
            <div class="absolute top-0 left-0 w-12 h-12 border-t border-l border-[#8C6A4A]/20 rounded-tl-sm pointer-events-none"></div>
            <div class="absolute top-0 right-0 w-12 h-12 border-t border-r border-[#8C6A4A]/20 rounded-tr-sm pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 border-b border-l border-[#8C6A4A]/20 rounded-bl-sm pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-12 h-12 border-b border-r border-[#8C6A4A]/20 rounded-br-sm pointer-events-none"></div>

            <!-- Skeleton Content -->
            <template v-if="props.report === null">
                <div class="flex flex-col items-center mb-16 space-y-4">
                    <SkeletonLoader width="60%" height="2rem" />
                    <SkeletonLoader width="40%" height="0.8rem" opacity="0.03" />
                </div>
                <div class="space-y-12">
                    <div v-for="i in 4" :key="i" class="space-y-4">
                        <SkeletonLoader width="30%" height="1.5rem" />
                        <div class="space-y-2">
                            <SkeletonLoader width="100%" height="1rem" opacity="0.03" />
                            <SkeletonLoader width="100%" height="1rem" opacity="0.03" />
                            <SkeletonLoader width="60%" height="1rem" opacity="0.03" />
                        </div>
                    </div>
                </div>
            </template>

            <ReportPreview 
                v-else
                ref="previewRef"
                :report="props.report" 
                @reset="handleReset"
                @saved="handleSaved"
                standalone
                class="flex-1"
            />
        </article>

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
            @confirm="confirmArchive"
        />

        <ReportProgressWidget />
        <Toast />
    </div>
</template>

<style scoped>
.font-serif {
    font-family: 'Cormorant Garamond', serif;
}
.font-sans {
    font-family: system-ui, -apple-system, sans-serif;
}
.action-trigger-btn {
    @apply inline-flex items-center gap-2 transition-all duration-300;
}
.action-icon-circle {
    @apply w-8 h-8 md:w-9 md:h-9 rounded-full border flex items-center justify-center transition-all duration-300;
}

/* Smooth Label Transitions */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
