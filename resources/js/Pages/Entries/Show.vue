<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import EntryMedia from '@/Components/Journal/EntryMedia.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Toast from '@/Components/UI/Toast.vue';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
import ReportProgressWidget from '@/Components/UI/ReportProgressWidget.vue';

const props = defineProps({
    entry: Object
});

const isReturning = ref(false);
const isEditing = ref(false);
const showDeleteModal = ref(false);

const handleReturn = () => {
    console.log("Navigating back to ledger...");
    isReturning.value = true;
    
    // Explicit 1.5s delay to ensure the premium animation is witnessed
    setTimeout(() => {
        router.visit(route('journal.index'));
    }, 1500);
};

const formatDate = (dateString) => {
    return new Intl.DateTimeFormat('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    }).format(new Date(dateString));
};


const form = useForm({
    title: props.entry.title,
    content:props.entry.content,
    entry_date:props.entry.entry_date ? props.entry.entry_date.substring(0, 10): '',
    file:props.entry.file,
});

const updateEntry = () => {
    if (!form.isDirty){
        isEditing.value = false;
        return;
    }

    
    form.patch(route('journal.update', props.entry.id), {
        onSuccess: () => {
            isEditing.value = false;
        },
        preserveScroll: true,
    });
}

const isProcessingDelete = ref(false);

const deleteEntry = async () => {
    if (!isProcessingDelete.value) {
        isProcessingDelete.value = true;
        
        // Brief pause for tactile feedback on the button
        await new Promise(resolve => setTimeout(resolve, 300));

        showDeleteModal.value = false;
        isReturning.value = true; // Show the 'Preserving to Ledger' loader for instant feedback
        
        router.delete(route('journal.destroy', props.entry.id), {
            onFinish: () => {
                isProcessingDelete.value = false;
            }
        });
    }
}

/**
 * Custom Directive: v-auto-resize
 * Automatically adjusts the height of a textarea as the user types.
 */
const vAutoResize = {
    mounted: (el) => {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
        el.addEventListener('input', () => {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        });
    },
    updated: (el) => {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }
};
</script>

<template>
    <Head :title="entry.title" />

    <!-- Standalone View Container (Aligned with System Background) -->
    <div class="text-parchment py-8 md:py-12 lg:py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center relative font-serif min-h-screen">
        
        <!-- Aligned Atmosphere (Matches Dashboard/Layout) -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
            <!-- Subtle Atmospheric Glow -->
            <div class="absolute top-1/4 left-1/4 w-[40rem] h-[40rem] bg-umber/5 blur-[120px] rounded-full"></div>
            <!-- Minimal Texture -->
            <div class="absolute inset-0 opacity-[0.1] bg-[url('https://www.transparenttextures.com/patterns/dust.png')]"></div>
        </div>

        <!-- Header Navigation & Actions -->
        <div class="w-full max-w-4xl z-30 mb-8 flex flex-wrap justify-between items-center gap-4">
             <a href="#" @click.prevent="handleReturn" class="inline-flex items-center gap-2 text-umber/60 hover:text-parchment transition-colors duration-300 group">
                <div class="w-8 h-8 rounded-full border border-umber/20 flex items-center justify-center group-hover:border-umber/60 transition-all">
                    <svg class="w-3 h-3 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">Return to Ledger</span>
            </a>

            <div class="flex items-center gap-4">
                <!-- Strike Action (Delete) -->
                <button 
                    v-if="!isEditing" 
                    @click="showDeleteModal = true" 
                    class="inline-flex items-center gap-2 text-red-500/40 hover:text-red-500 transition-colors duration-300 group mr-2"
                >
                    <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">Strike Record</span>
                    <div class="w-8 h-8 rounded-full border border-red-500/10 flex items-center justify-center group-hover:border-red-500/40 transition-all bg-red-500/5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </button>

                <button 
                    v-if="!isEditing" 
                    @click="isEditing = true" 
                    class="inline-flex items-center gap-2 text-parchment/40 hover:text-parchment transition-colors duration-300 group"
                >
                    <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">Amend Lore</span>
                    <div class="w-8 h-8 rounded-full border border-parchment/10 flex items-center justify-center group-hover:border-parchment/40 transition-all bg-parchment/5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                </button>
                
                <template v-else>
                    <button 
                        @click="isEditing = false" 
                        class="inline-flex items-center gap-2 text-[#8C6A4A]/60 hover:text-[#C9B79C] transition-colors duration-300"
                    >
                        <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">Discard</span>
                    </button>
                    <button
                        @click="updateEntry" 
                        class="inline-flex items-center gap-2 text-[#C9B79C]/80 hover:text-white transition-colors duration-300 group"
                    >
                        <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase whitespace-nowrap">Seal Decree</span>
                        <div class="w-8 h-8 rounded-full border border-[#C9B79C]/20 flex items-center justify-center group-hover:border-[#C9B79C] transition-all bg-[#C9B79C]/10 shadow-[0_0_15px_rgba(201,183,156,0.1)]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Global Tome Loader -->
        <TomeLoader :show="isReturning" message="Preserving to Ledger..." />

        <!-- Main Entry Container -->
        <article class="w-full max-w-4xl relative z-30 bg-[#2D2D2D]/40 backdrop-blur-[2px] border border-white/5 p-6 sm:p-10 md:p-16 rounded-sm shadow-2xl animate-fade-in-up group">
            
            <!-- Ornamental Border Corners (Bronze Indication) -->
            <div class="absolute top-0 left-0 w-12 h-12 border-t border-l border-[#8B6D45]/30 rounded-tl-sm pointer-events-none group-hover:opacity-0 transition-opacity duration-700"></div>
            <div class="absolute top-0 right-0 w-12 h-12 border-t border-r border-[#8B6D45]/30 rounded-tr-sm pointer-events-none group-hover:opacity-0 transition-opacity duration-700"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 border-b border-l border-[#8B6D45]/30 rounded-bl-sm pointer-events-none group-hover:opacity-0 transition-opacity duration-700"></div>
            <div class="absolute bottom-0 right-0 w-12 h-12 border-b border-r border-[#8B6D45]/30 rounded-br-sm pointer-events-none group-hover:opacity-0 transition-opacity duration-700"></div>

            <!-- Header -->
            <header class="text-center mb-10 md:mb-16 relative">
                <div class="mb-6 flex flex-col items-center gap-4">
                     <div class="h-12 w-[1px] bg-gradient-to-b from-transparent via-umber/40 to-transparent"></div>
                     
                     <!-- Date Display/Input -->
                     <div class="relative group">
                         <div v-if="!isEditing" class="text-[10px] sm:text-xs font-cinzel tracking-[0.4em] text-umber uppercase bg-umber/5 px-5 py-1.5 rounded-full border border-umber/10 transition-all whitespace-nowrap">
                            {{ formatDate(entry.entry_date) }}
                         </div>
                         <input
                            v-else
                            v-model="form.entry_date"
                            type="date"
                            class="bg-black/20 border border-umber/20 focus:border-umber/60 focus:ring-0 text-[10px] sm:text-xs font-cinzel tracking-[0.4em] text-umber uppercase px-5 py-1.5 rounded-full text-center transition-all dark:[color-scheme:dark]"
                         >
                     </div>
                </div>
                
                <!-- Title Section -->
                <div class="relative">
                    <h1
                        v-if="!isEditing"
                        class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-cinzel font-bold text-parchment leading-tight tracking-tight drop-shadow-sm px-2 break-words text-center">
                        {{ entry.title }}
                    </h1>
                    <textarea
                        v-else
                        v-model="form.title"
                        rows="1"
                        v-auto-resize
                        class="bg-transparent border-none focus:ring-0 w-full text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-cinzel font-bold text-parchment text-center leading-tight tracking-tight selection:bg-umber/20 p-0 resize-none overflow-hidden placeholder:text-parchment/20"
                        placeholder="Title of Lore..."
                    ></textarea>
                </div>

                <div class="mt-8 w-16 sm:w-24 h-[1px] bg-gradient-to-r from-transparent via-umber/40 to-transparent mx-auto"></div>
            </header>

            <!-- Content -->
            <div class="prose prose-lg md:prose-xl prose-invert max-w-none text-parchment/80 font-serif leading-relaxed tracking-wide text-justify selection:bg-umber/20 mb-12 md:mb-20">
                 <div class="relative min-h-[300px]">
                    <div v-if="!isEditing" class="first-letter:text-5xl first-letter:font-cinzel first-letter:text-parchment first-letter:mr-3 first-letter:float-left first-letter:leading-[0.8] break-words overflow-hidden">
                        <p class="whitespace-pre-wrap text-[17px] sm:text-lg md:text-xl text-justify">{{ entry.content }}</p>
                    </div>
                    <textarea
                        v-else
                        v-model="form.content"
                        rows="12"
                        class="bg-black/10 border-none focus:ring-0 w-full whitespace-pre-wrap text-[17px] sm:text-lg md:text-xl font-serif leading-relaxed p-0 min-h-[400px] transition-all duration-300 resize-none placeholder:text-[#C9B79C]/20"
                        placeholder="Inscribe the history..."
                    ></textarea>
                </div>
            </div>

            <!-- Media Section -->
            <EntryMedia :entry="entry" class="mb-12 md:mb-20" />

            <!-- Footer -->
             <div class="pt-8 sm:pt-12 border-t border-umber/10 flex flex-col items-center gap-6 md:gap-8">
                <div v-if="entry.file">
                    <a :href="'/storage/' + entry.file" target="_blank" class="flex items-center gap-3 text-umber/60 hover:text-parchment transition-all px-6 py-2.5 border border-umber/10 hover:border-umber/30 rounded-sm bg-umber/5 group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <span class="text-[10px] sm:text-xs font-cinzel tracking-widest uppercase">Attached Parchment</span>
                    </a>
                </div>
                
                <div class="flex flex-col items-center opacity-30">
                    <div class="flex gap-2 mb-3">
                         <div class="w-1.5 h-1.5 rounded-full bg-umber"></div>
                         <div class="w-1.5 h-1.5 rounded-full bg-umber"></div>
                         <div class="w-1.5 h-1.5 rounded-full bg-umber"></div>
                    </div>
                    <span class="text-[9px] font-cinzel uppercase tracking-[0.5em] text-umber">Lore End</span>
                </div>
            </div>
        </article>
        
        <!-- Re-added Toast for standalone functionality -->
        <Toast />

        <!-- Confirmation Modal for Deletion -->
        <ConfirmationModal 
            :show="showDeleteModal"
            :processing="isProcessingDelete"
            title="Banish to Vault?"
            message="This lore will be moved to the Sunken Vault. It will be hidden from the ledger but can be recovered from the forgotten depths."
            confirm-text="Banish Record"
            cancel-text="Preserve"
            type="danger"
            @close="showDeleteModal = false"
            @confirm="deleteEntry"
        />

        <ReportProgressWidget />
    </div>
</template>

<style scoped>
.dust-particles {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: 
        radial-gradient(1px 1px at 20px 30px, #C9B79C, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 40px 70px, #C9B79C, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 50px 160px, #C9B79C, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 90px 40px, #C9B79C, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 130px 80px, #C9B79C, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 160px 120px, #C9B79C, rgba(0,0,0,0));
    background-repeat: repeat;
    background-size: 200px 200px;
    animation: dust 60s linear infinite;
}

@keyframes dust {
    from { transform: translateY(0); }
    to { transform: translateY(-200px); }
}

.ledger-shimmer {
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { filter: drop-shadow(0 0 2px rgba(140, 106, 74, 0.2)); transform: translateY(0); }
    50% { filter: drop-shadow(0 0 15px rgba(201, 183, 156, 0.3)); transform: translateY(-5px); }
}
</style>
