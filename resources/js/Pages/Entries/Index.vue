<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';

// Native Debounce Implementation
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import JournalCard from '@/Components/Journal/JournalCard.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
import { useTabSync } from '@/Composables/useTabSync';

const props = defineProps({
    entries: Object, // Paginated object
    filters: Object
});

// Setup tab sync
const { broadcastUpdate } = useTabSync(['entries']);

const searchQuery = ref(props.filters.search || '');
const isNavigating = ref(false);
const showDeleteModal = ref(false);
const entryToDelete = ref(null);
const isLoadingMore = ref(false);
const searchInput = ref(null);

const handleGlobalKeydown = (e) => {
    // Ctrl+K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.value?.focus();
    }
};

// Local list for infinite scroll
const allEntries = ref(props.entries?.data ? [...props.entries.data] : []);

// Watch for new entries (pagination or search)
watch(() => props.entries, (newVal, oldVal) => {
    if (newVal.current_page === 1) {
        allEntries.value = [...newVal.data];
    } else if (newVal.current_page > oldVal.current_page) {
        allEntries.value = [...allEntries.value, ...newVal.data];
    }
}, { deep: true });

// Debounced Search
const performSearch = debounce((q) => {
    router.get(route('journal.index'), { search: q }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['entries', 'filters']
    });
}, 400);

watch(searchQuery, (q) => performSearch(q));

// Infinite Scroll Logic
const loaderTarget = ref(null);
let observer = null;

const loadMore = () => {
    if (props.entries.next_page_url && !isLoadingMore.value) {
        isLoadingMore.value = true;
        router.get(props.entries.next_page_url, {}, {
            preserveState: true,
            preserveScroll: true,
            only: ['entries'],
            onFinish: () => {
                isLoadingMore.value = false;
            }
        });
    }
};

onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMore();
        }
    }, { threshold: 0.1 });

    if (loaderTarget.value) observer.observe(loaderTarget.value);
    window.addEventListener('keydown', handleGlobalKeydown);
});

onUnmounted(() => {
    if (observer) observer.disconnect();
    window.removeEventListener('keydown', handleGlobalKeydown);
});

const handleEntryClick = (id) => {
    router.visit(route('journal.show', id));
};

const confirmDelete = (id) => {
    entryToDelete.value = id;
    showDeleteModal.value = true;
};

const isProcessingDelete = ref(false);

const deleteEntry = async () => {
    if (entryToDelete.value && !isProcessingDelete.value) {
        const idToRemove = entryToDelete.value;
        isProcessingDelete.value = true;
        
        // Give the user a brief moment (300ms) to see the 'Processing' state 
        // so they know their click was registered before it vanishes cinematically
        await new Promise(resolve => setTimeout(resolve, 300));

        // 1. Close modal and remove from UI (Optimistic)
        showDeleteModal.value = false;
        allEntries.value = allEntries.value.filter(e => (e.id || e._id) !== idToRemove);
        
        broadcastUpdate(); 

        router.delete(route('journal.destroy', idToRemove), {
            preserveScroll: true,
            onFinish: () => {
                isProcessingDelete.value = false;
                entryToDelete.value = null;
            },
            onError: () => {
                isProcessingDelete.value = false;
                router.reload();
            }
        });
    }
};


</script>

<template>
    <Head title="Journal Entries" />

    <AuthenticatedLayout 
        title="The <span class='text-[#8C6A4A]'>Chronicler's</span> Ledger"
        subtitle="Lore of your journeys"
    >
        <div class="p-4 md:p-6 lg:p-8 flex flex-col font-serif relative">
            <!-- Search & Count Bar (Zen Simplicity) -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-16 relative z-10">
                <div class="relative w-full max-w-lg group">
                    <div class="relative flex items-center">
                        <input 
                            ref="searchInput"
                            v-model="searchQuery"
                            type="search" 
                            placeholder="Seek through the echoes..."
                            class="w-full bg-void/20 border border-brass/10 rounded-lg px-12 py-3 text-parchment placeholder-brass/20 focus:border-brass/40 focus:ring-0 transition-all font-serif italic text-base"
                        >
                        <svg class="absolute left-4 h-4 w-4 text-brass/20 group-focus-within:text-brass/60 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        
                        <!-- Shortcut Indicator -->
                        <div class="absolute right-4 px-1.5 py-0.5 rounded border border-brass/10 bg-white/5 text-[8px] text-brass/30 font-cinzel tracking-widest pointer-events-none group-focus-within:opacity-0 transition-opacity uppercase">
                            Ctrl K
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-brass/40">
                    <div class="h-[1px] w-8 bg-brass/20"></div>
                    <span class="text-[10px] font-cinzel uppercase tracking-[0.4em]">{{ props.entries.total }} Chronicles</span>
                </div>
            </div>

            <!-- Entries Grid -->
            <div class="flex-1 overflow-y-auto pr-0 md:pr-2 scrollbar-hide relative z-10">
                <template v-if="allEntries.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-12 pb-24">
                        <JournalCard 
                            v-for="entry in allEntries" 
                            :key="entry.id || entry._id"
                            :entry="entry"
                            @click="handleEntryClick(entry.id || entry._id)"
                            @delete="confirmDelete"
                        />
                    </div>

                    <!-- Infinite Scroll Sentinel -->
                    <div 
                        ref="loaderTarget" 
                        id="infinite-sentinel"
                        class="w-full py-16 flex flex-col items-center justify-center gap-4 transition-all duration-1000"
                        :class="isLoadingMore ? 'opacity-100' : 'opacity-0'"
                    >
                        <div class="relative w-8 h-8">
                            <div class="absolute inset-0 border border-brass/10 rounded-full animate-spin-slow"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-1 h-1 bg-brass/40 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                        <p class="text-[9px] font-cinzel text-brass/30 uppercase tracking-[0.5em]">
                            Consulting the void...
                        </p>
                    </div>
                </template>

                <!-- Empty State (Zen Style) -->
                <div v-else class="flex-1 flex flex-col items-center justify-center py-40 text-center relative z-10">
                    <div class="relative">
                        <div class="h-24 w-24 rounded-full border border-brass/5 flex items-center justify-center mb-10 mx-auto group">
                            <svg class="w-10 h-10 text-brass/10 group-hover:text-brass/20 transition-all duration-1000" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-cream text-4xl font-cinzel font-medium mb-4 tracking-wider opacity-80">The Ledger is Silent</h3>
                        <p class="text-brass/30 text-[10px] uppercase tracking-[0.6em] font-serif">No echoes found in this search</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>


    <TomeLoader :show="isNavigating" message="Consulting the Archives..." />

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
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}



@keyframes pulse-slow {
    0%, 100% { opacity: 0.02; transform: scale(1); }
    50% { opacity: 0.05; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 10s ease-in-out infinite;
}

/* Dust Particle Animation */
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
</style>
