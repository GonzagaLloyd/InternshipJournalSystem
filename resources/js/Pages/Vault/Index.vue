<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';

const props = defineProps({
    entries: Array,
    tasks: Array
});

const activeTab = ref('entries'); // entries, tasks
const showRestoreModal = ref(false);
const showDeletePermanentModal = ref(false);
const processingItem = ref(null);
const processingType = ref(null); // 'entry' or 'task'

const formatDate = (dateString) => {
    if (!dateString) return 'Time Unknown';
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(dateString));
};

const openRestoreModal = (item, type) => {
    processingItem.value = item;
    processingType.value = type;
    showRestoreModal.value = true;
};

const openDeletePermanentModal = (item, type) => {
    processingItem.value = item;
    processingType.value = type;
    showDeletePermanentModal.value = true;
};

const handleRestore = () => {
    const id = processingItem.value.id || processingItem.value._id;
    const routeName = processingType.value === 'entry' ? 'vault.journal.restore' : 'vault.tasks.restore';
    
    router.post(route(routeName, id), {}, {
        onSuccess: () => {
            showRestoreModal.value = false;
            processingItem.value = null;
        }
    });
};

const handlePermanentDelete = () => {
    const id = processingItem.value.id || processingItem.value._id;
    const routeName = processingType.value === 'entry' ? 'vault.journal.force-delete' : 'vault.tasks.force-delete';
    
    router.delete(route(routeName, id), {
        onSuccess: () => {
            showDeletePermanentModal.value = false;
            processingItem.value = null;
        }
    });
};

</script>

<template>
    <Head title="The Sunken Vault" />

    <AuthenticatedLayout>
        <div class="p-4 md:p-8 lg:p-12 min-h-full flex flex-col font-serif relative overflow-hidden">
            
            <!-- Vault Atmosphere -->
            <div class="fixed inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-transparent"></div>
                <div class="absolute top-[20%] right-[10%] w-[50rem] h-[50rem] bg-[#8C6A4A]/5 blur-[150px] rounded-full"></div>
            </div>

            <!-- Header -->
            <div class="relative z-10 mb-12">
                <div class="flex items-center gap-6 mb-4">
                    <div class="h-[1px] w-12 bg-gradient-to-r from-transparent to-[#8C6A4A]/60"></div>
                    <span class="text-[10px] uppercase tracking-[0.6em] text-[#8C6A4A] font-black">The Forgotten Depths</span>
                    <div class="h-[1px] flex-1 bg-gradient-to-r from-[#8C6A4A]/60 to-transparent"></div>
                </div>
                
                <h1 class="text-4xl md:text-6xl font-cinzel font-bold text-[#C9B79C] tracking-tight mb-4">
                    The <span class="text-[#8C6A4A]/80 italic">Sunken</span> Vault
                </h1>
                <p class="text-[#8C6A4A]/60 max-w-2xl text-sm md:text-base italic leading-relaxed">
                    Echoes of shattered decrees and stricken lore. Here lies what was deemed worthy of the pyre, 
                    waiting for a final judgment or a chance to rise from the ashes.
                </p>
            </div>

            <!-- Navigation Tabs -->
            <div class="flex items-center gap-8 border-b border-white/5 mb-10 relative z-10">
                <button 
                    @click="activeTab = 'entries'"
                    :class="activeTab === 'entries' ? 'text-[#C9B79C] border-[#8C6A4A] border-b-2 -mb-[2px]' : 'text-[#8C6A4A]/40 hover:text-[#C9B79C]'"
                    class="pb-4 px-2 transition-all duration-500 font-cinzel text-xs uppercase tracking-[0.3em] font-bold"
                >
                    Stricken Lore ({{ entries.length }})
                </button>
                <button 
                    @click="activeTab = 'tasks'"
                    :class="activeTab === 'tasks' ? 'text-[#C9B79C] border-[#8C6A4A] border-b-2 -mb-[2px]' : 'text-[#8C6A4A]/40 hover:text-[#C9B79C]'"
                    class="pb-4 px-2 transition-all duration-500 font-cinzel text-xs uppercase tracking-[0.3em] font-bold"
                >
                    Forgotten Deeds ({{ tasks.length }})
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-1 relative z-10">
                
                <!-- Entries Tab -->
                <div v-if="activeTab === 'entries'" class="space-y-6">
                    <div v-if="entries.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div 
                            v-for="entry in entries" 
                            :key="entry.id || entry._id"
                            class="group relative bg-[#2D2D2D]/20 border border-white/5 p-8 rounded-sm hover:border-[#8C6A4A]/40 transition-all duration-700"
                        >
                            <!-- Ghostly Overlay -->
                            <div class="absolute inset-0 bg-[#8C6A4A]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700 blur-xl pointer-events-none"></div>
                            
                            <!-- Card Content -->
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-6">
                                    <span class="text-[9px] uppercase tracking-widest text-[#8C6A4A]/60 font-serif">Stricken on {{ formatDate(entry.deleted_at) }}</span>
                                    <div class="flex gap-3 h-0 group-hover:h-auto overflow-hidden opacity-0 group-hover:opacity-100 transition-all duration-500">
                                        <button @click="openRestoreModal(entry, 'entry')" class="text-[#8C6A4A]/60 hover:text-[#C9B79C] transition-colors" title="Heal from Ashes">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v4L8 12m4-2l4 4m-2 4h5a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2h5" /></svg>
                                        </button>
                                        <button @click="openDeletePermanentModal(entry, 'entry')" class="text-red-900/40 hover:text-red-600 transition-colors" title="Burn Forever">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="text-xl font-cinzel text-[#C9B79C]/80 group-hover:text-[#C9B79C] transition-colors mb-4 truncate">{{ entry.title }}</h3>
                                <p class="text-sm text-[#8C6A4A]/40 line-clamp-3 italic mb-6 leading-relaxed">{{ entry.content }}</p>
                                <div class="h-[1px] w-full bg-gradient-to-r from-[#8C6A4A]/20 to-transparent"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-40 flex flex-col items-center justify-center opacity-20 border border-white/5 rounded-sm">
                        <svg class="w-20 h-20 mb-6 text-[#8C6A4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <p class="font-cinzel tracking-widest text-xs uppercase text-[#8C6A4A]">No Stricken Lore Found</p>
                    </div>
                </div>

                <!-- Tasks Tab -->
                <div v-if="activeTab === 'tasks'" class="space-y-4">
                    <div v-if="tasks.length > 0" class="max-w-4xl space-y-3">
                        <div 
                            v-for="task in tasks" 
                            :key="task.id || task._id"
                            class="group flex items-center justify-between bg-[#2D2D2D]/20 border border-white/5 px-6 py-5 rounded-sm hover:border-[#8C6A4A]/20 transition-all duration-300"
                        >
                            <div class="flex flex-col gap-1">
                                <span class="text-base text-[#C9B79C]/70 group-hover:text-[#C9B79C] transition-colors font-medium">{{ task.name }}</span>
                                <span class="text-[9px] uppercase tracking-widest text-[#8C6A4A]/40 italic">Banished on {{ formatDate(task.deleted_at) }}</span>
                            </div>
                            <div class="flex items-center gap-6">
                                <span class="text-[9px] uppercase tracking-[0.2em] text-[#8C6A4A]/40 border border-[#8C6A4A]/10 px-2 py-0.5 rounded-sm">{{ task.priority || 'Medium' }}</span>
                                <div class="flex gap-4">
                                    <button @click="openRestoreModal(task, 'task')" class="text-[#8C6A4A]/40 hover:text-[#C9B79C] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    </button>
                                    <button @click="openDeletePermanentModal(task, 'task')" class="text-red-900/20 hover:text-red-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-40 flex flex-col items-center justify-center opacity-20 border border-white/5 rounded-sm">
                        <svg class="w-20 h-20 mb-6 text-[#8C6A4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="font-cinzel tracking-widest text-xs uppercase text-[#8C6A4A]">No Forgotten Deeds Recoverable</p>
                    </div>
                </div>

            </div>

        </div>

        <!-- Restore Confirmation -->
        <ConfirmationModal 
            :show="showRestoreModal"
            title="Resurrect Records?"
            :message="`Shall we call this ${processingType === 'entry' ? 'lore' : 'deed'} back from the void? It will return to its original place in the archives.`"
            confirm-text="Heal Records"
            cancel-text="Let it Rest"
            type="info"
            @close="showRestoreModal = false"
            @confirm="handleRestore"
        />

        <!-- Permanent Delete Confirmation -->
        <ConfirmationModal 
            :show="showDeletePermanentModal"
            title="Final Oblivion?"
            message="This action will erase the records from existence itself. Not even echoes will remain. Proceed with the final burn?"
            confirm-text="Erase Forever"
            cancel-text="Spare Echo"
            type="danger"
            @close="showDeletePermanentModal = false"
            @confirm="handlePermanentDelete"
        />

    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
