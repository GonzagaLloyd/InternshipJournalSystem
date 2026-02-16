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
});

onUnmounted(() => {
    if (observer) observer.disconnect();
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
            <!-- Search & Count Bar (Fixed at top of content) -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12 relative z-10">
                <div class="relative w-full max-w-md group">
                    <input 
                        v-model="searchQuery"
                        type="search" 
                        placeholder="Seek through the echoes..."
                        class="w-full bg-[#1B1B1B]/40 border border-[#8C6A4A]/20 rounded-sm px-10 py-3 text-[#C9B79C] placeholder-[#8C6A4A]/40 focus:border-[#8C6A4A]/60 focus:ring-0 transition-all font-serif italic"
                    >
                    <svg class="absolute left-3 top-3.5 h-4 w-4 text-[#8C6A4A]/40 group-focus-within:text-[#8C6A4A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                <div class="flex items-center gap-4 text-[#8C6A4A]">
                    <span class="text-[10px] uppercase font-black tracking-widest">{{ props.entries.total }} Chronicles Found</span>
                    <div class="h-4 w-[1px] bg-[#8C6A4A]/20"></div>
                </div>
            </div>

            <!-- Entries Grid -->
            <div class="flex-1 overflow-y-auto pr-0 md:pr-2 scrollbar-hide relative z-10">
                <template v-if="allEntries.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-8 lg:gap-12 pb-20">
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
                        class="w-full py-12 flex flex-col items-center justify-center gap-4 transition-all duration-500"
                        :class="isLoadingMore ? 'opacity-100' : 'opacity-0'"
                    >
                        <div class="relative w-10 h-10">
                            <div class="absolute inset-0 border border-[#8C6A4A]/20 rounded-full animate-[spin_3s_linear_infinite]"></div>
                            <div class="absolute inset-1 border border-dashed border-[#8C6A4A]/40 rounded-full animate-[spin_5s_linear_infinite_reverse]"></div>
                        </div>
                        <p class="text-[9px] font-cinzel text-[#8C6A4A] uppercase tracking-[0.3em] animate-pulse">
                            Whispering to the void...
                        </p>
                    </div>
                </template>

                <!-- Empty State -->
                <div v-else class="flex-1 flex flex-col items-center justify-center py-40 text-center relative z-10">
                    <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                        <svg class="w-[500px] h-[500px] text-[#C9B79C] animate-pulse-slow" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                    </div>
                    <div class="relative">
                        <div class="h-24 w-24 rounded-full border border-[#8C6A4A]/20 flex items-center justify-center mb-10 mx-auto group">
                            <svg class="w-12 h-12 text-[#8C6A4A]/40 group-hover:text-[#8C6A4A] transition-all duration-1000" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-[#C9B79C] text-5xl font-cinzel font-bold mb-4 tracking-tight">The Ledger is Silent</h3>
                        <p class="text-[#8C6A4A] text-sm uppercase tracking-[0.5em] font-serif opacity-40">No echoes found in this search</p>
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
