<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReportPreview from '@/Components/Reports/ReportPreview.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';
import ReportProgressWidget from '@/Components/UI/ReportProgressWidget.vue';

const props = defineProps({
    report: Object
});

const previewRef = ref(null);
const isReturning = ref(false);
const isScrolled = ref(false);

// Helper to access internal state of ReportPreview
const isSaving = computed(() => previewRef.value?.isSaving || false);

const handleReturn = () => {
    router.visit(route('reports.index'));
};

const handleSave = () => previewRef.value?.saveReport();
const handleCancel = () => handleReturn();

const handleSaved = (savedReport) => {
    // Redirect to the show page for the newly created report
    router.visit(route('reports.show', savedReport.id || savedReport._id));
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

// Force edit mode on mount for manual creation
onMounted(() => {
    if (previewRef.value) {
        previewRef.value.toggleEdit();
    }
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

</script>

<template>
    <Head title="Create Manual Chronicle" />

    <div class="text-[#C9B79C] py-8 md:py-12 lg:py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center relative font-serif min-h-screen bg-[#1B1B1B]">
        
        <!-- Divine Atmosphere -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-[40rem] h-[40rem] bg-[#8C6A4A]/5 blur-[120px] rounded-full"></div>
            <div class="absolute inset-0 opacity-[0.1] bg-[url('https://www.transparenttextures.com/patterns/dust.png')]"></div>
        </div>

        <TomeLoader :show="isReturning" message="Returning to Archives..." />

        <!-- Header Navigation & Actions (Sticky Responsive) -->
        <div :class="[
            'sticky top-0 w-full z-40 transition-all duration-500 flex justify-center px-4 sm:px-6',
            isScrolled 
                ? 'bg-[#1B1B1B]/95 backdrop-blur-2xl border-b border-white/[0.08] py-2 shadow-[0_4px_30px_rgba(0,0,0,0.5)]' 
                : 'bg-transparent py-6 md:py-8'
        ]">
            <div :class="['w-full max-w-4xl flex items-center justify-between gap-4 transition-all duration-500', isScrolled ? 'md:max-w-5xl' : '']">
                <!-- Return Link -->
                <a href="#" @click.prevent="handleReturn" class="inline-flex items-center gap-2 text-[#8C6A4A]/60 hover:text-[#C9B79C] transition-all duration-300 group">
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-full border border-[#8C6A4A]/20 flex items-center justify-center group-hover:border-[#8C6A4A]/60 transition-all bg-[#8C6A4A]/5">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="hidden sm:inline text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">
                        {{ isScrolled ? 'Archives' : 'Return to Ledger' }}
                    </span>
                </a>

                <div class="flex items-center gap-2 sm:gap-4 md:gap-6">
                    <button @click="handleCancel" class="text-[10px] font-cinzel tracking-widest uppercase text-[#8C6A4A]/60 hover:text-[#C9B79C] px-2 py-2 transition-colors">
                        Discard
                    </button>
                    
                    <button @click="handleSave" :disabled="isSaving" class="inline-flex items-center gap-2 group transition-all duration-300">
                        <span class="hidden sm:inline text-[10px] font-cinzel tracking-[0.2em] uppercase text-[#C9B79C]/80 group-hover:text-white">
                            {{ isSaving ? 'Sealing...' : 'Seal Decree' }}
                        </span>
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full border border-[#C9B79C]/30 flex items-center justify-center group-hover:border-[#C9B79C] transition-all bg-[#C9B79C]/10 shadow-[0_0_20px_rgba(201,183,156,0.1)] group-hover:shadow-[0_0_30px_rgba(201,183,156,0.2)]">
                            <svg v-if="!isSaving" class="w-4 h-4 text-[#C9B79C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg v-else class="animate-spin w-4 h-4 text-[#C9B79C]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Report Content -->
        <article class="w-full max-w-4xl relative z-30 bg-[#2D2D2D]/40 backdrop-blur-[2px] border border-white/5 p-4 sm:p-10 md:p-16 rounded-sm shadow-2xl animate-fade-in-up">
            <!-- Ornamental Border Corners -->
            <div class="absolute top-0 left-0 w-8 h-8 md:w-12 md:h-12 border-t border-l border-[#8C6A4A]/20 rounded-tl-sm pointer-events-none"></div>
            <div class="absolute top-0 right-0 w-8 h-8 md:w-12 md:h-12 border-t border-r border-[#8C6A4A]/20 rounded-tr-sm pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 md:w-12 md:h-12 border-b border-l border-[#8C6A4A]/20 rounded-bl-sm pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 md:w-12 md:h-12 border-b border-r border-[#8C6A4A]/20 rounded-br-sm pointer-events-none"></div>

            <ReportPreview 
                ref="previewRef"
                :report="props.report" 
                @saved="handleSaved"
                standalone
                class="flex-1"
            />
        </article>
        
        <ReportProgressWidget class="fixed top-24 right-8 z-[100]" />
    </div>
</template>

<style scoped>
.font-serif {
    font-family: 'Cormorant Garamond', serif;
}
.font-cinzel {
    font-family: 'Cinzel', serif;
}
</style>
