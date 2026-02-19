<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SkeletonLoader from '@/Components/UI/SkeletonLoader.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';
import TaskItem from '@/Components/Tasks/TaskItem.vue';
import TaskCreateModal from '@/Components/Tasks/TaskCreateModal.vue';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
import { useTabSync } from '@/Composables/useTabSync';

const props = defineProps({
    tasks: {
        type: Array,
        default: null
    }
});

// Setup tab sync
const { broadcastUpdate } = useTabSync(['tasks']);

const isNavigating = ref(false);
const showCreateModal = ref(false);
const showDeleteConfirm = ref(false);
const taskToDelete = ref(null);
const processingTaskId = ref(null);

const toggleTask = (task) => {
    console.log('Task object:', task);
    console.log('Task ID:', task.id);
    
    if (!task.id) {
        console.error('Task ID is missing!', task);
        return;
    }
    
    processingTaskId.value = task.id;
    
    router.patch(route('tasks.toggle', { task: task.id }), {}, {
        preserveScroll: true,
        onFinish: () => {
            processingTaskId.value = null;
        }
    });
};

const deleteTask = (task) => {
    taskToDelete.value = task;
    showDeleteConfirm.value = true;
};

const isProcessingDelete = ref(false);

const confirmDeletion = async () => {
    const task = taskToDelete.value;
    if (!task || !task.id || isProcessingDelete.value) return;

    isProcessingDelete.value = true;
    
    // Tactile feedback pause
    await new Promise(resolve => setTimeout(resolve, 300));

    broadcastUpdate(); // Notify Vault

    router.delete(route('tasks.destroy', { task: task.id }), {
        preserveScroll: true,
        onFinish: () => {
            showDeleteConfirm.value = false;
            taskToDelete.value = null;
            isProcessingDelete.value = false;
        }
    });
};

const sortedTasks = computed(() => {
    if (!props.tasks) return [];
    return [...props.tasks].sort((a, b) => {
        if (a.completed !== b.completed) {
            return a.completed ? 1 : -1;
        }
        return new Date(b.created_at) - new Date(a.created_at);
    });
});
</script>

<template>
    <Head title="Scriptorium - Tasks" />

    <AuthenticatedLayout 
        title="Active <span class='text-[#8C6A4A]'>Decrees</span>"
        subtitle="The Scriptorium's Mandate"
    >
        <div class="p-4 md:p-10 lg:p-12 flex flex-col font-serif relative min-h-full">
            <div class="max-w-6xl mx-auto w-full flex-1 flex flex-col relative z-20">
                <!-- Action Bar -->
                <div class="flex flex-col-reverse sm:flex-row justify-between items-center gap-6 mb-12 relative z-10 w-full">
                    <div class="hidden sm:flex flex-col">
                        <span class="text-[10px] uppercase tracking-[0.4em] text-[#8C6A4A] font-black">Scroll to reveal</span>
                        <div class="h-[1px] w-12 bg-[#8C6A4A]/20 mt-2"></div>
                    </div>

                    <button 
                        @click="showCreateModal = true"
                        class="w-full sm:w-auto group flex items-center justify-center gap-4 px-8 md:px-10 py-4 bg-[#8C6A4A]/10 border border-[#8C6A4A]/30 rounded-sm text-[#C9B79C] hover:bg-[#8C6A4A] transition-all duration-500 shadow-lg hover:shadow-[#8C6A4A]/20"
                    >
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] font-serif">Append Decree</span>
                        <div class="h-6 w-6 rounded-full border border-[#C9B79C]/30 flex items-center justify-center group-hover:border-[#C9B79C] transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12m6-6H6" />
                            </svg>
                        </div>
                    </button>
                </div>

                <!-- Task List -->
                <div class="flex-1 overflow-y-auto scrollbar-hide pr-2">
                    <!-- Skeleton State -->
                    <template v-if="props.tasks === null">
                        <div class="space-y-12">
                            <div v-for="i in 2" :key="i" class="space-y-6">
                                <div class="px-8 flex items-center gap-4">
                                    <SkeletonLoader width="12rem" height="1rem" opacity="0.05" />
                                    <div class="h-[1px] flex-1 bg-[#8C6A4A]/10"></div>
                                </div>
                                <div class="bg-[#2D2D2D]/20 rounded-[2rem] border border-white/5 overflow-hidden divide-y divide-[#3d352e]/10">
                                    <div v-for="j in 3" :key="j" class="px-8 py-6 flex items-center justify-between">
                                        <div class="flex items-center gap-6 flex-1">
                                            <SkeletonLoader width="1.5rem" height="1.5rem" borderRadius="50%" />
                                            <div class="space-y-2 flex-1">
                                                <SkeletonLoader width="40%" height="1.2rem" />
                                                <SkeletonLoader width="20%" height="0.6rem" opacity="0.03" />
                                            </div>
                                        </div>
                                        <SkeletonLoader width="4rem" height="1.5rem" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <!-- Outstanding Decrees (Active) -->
                        <div v-if="sortedTasks.some(t => !t.completed)" class="mb-12">
                            <div class="px-8 mb-6 flex items-center gap-4">
                                <span class="text-[10px] uppercase tracking-[0.4em] text-[#8C6A4A] font-black">Outstanding Decrees</span>
                                <div class="h-[1px] flex-1 bg-[#8C6A4A]/10"></div>
                            </div>
                            <div class="divide-y divide-[#3d352e]/10 bg-[#2D2D2D]/20 rounded-[2rem] border border-white/5 overflow-hidden">
                                <TaskItem 
                                    v-for="task in sortedTasks.filter(t => !t.completed)" 
                                    :key="task.id"
                                    :task="task"
                                    :processing="processingTaskId === task.id"
                                    @toggle="toggleTask"
                                    @delete="deleteTask"
                                />
                            </div>
                        </div>

                        <!-- Fulfilled Records (Completed) -->
                        <div v-if="sortedTasks.some(t => t.completed)" class="opacity-50">
                            <div class="px-8 mb-6 flex items-center gap-4">
                                <span class="text-[10px] uppercase tracking-[0.4em] text-[#8C6A4A]/40 font-black">Fulfilled Records</span>
                                <div class="h-[1px] flex-1 bg-[#8C6A4A]/5"></div>
                            </div>
                            <div class="divide-y divide-[#3d352e]/5">
                                <TaskItem 
                                    v-for="task in sortedTasks.filter(t => t.completed)" 
                                    :key="task.id"
                                    :task="task"
                                    :processing="processingTaskId === task.id"
                                    @toggle="toggleTask"
                                    @delete="deleteTask"
                                />
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="tasks.length === 0" class="flex flex-col items-center justify-center py-32 animate-pulse-slow">
                            <div class="relative mb-10">
                                <svg class="w-32 h-32 text-[#3d3d3d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <div class="absolute inset-0 bg-radial-gradient from-[#8C6A4A]/10 to-transparent blur-2xl"></div>
                            </div>
                            <h3 class="text-3xl font-cinzel text-[#8C6A4A] uppercase tracking-[0.5em] opacity-40">The Ledger is Satisfied</h3>
                            <p class="text-[#3d3d3d] text-[10px] uppercase tracking-[0.4em] mt-4 font-serif font-black">No decrees remain outstanding</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Create Modal Component -->
        <TaskCreateModal :show="showCreateModal" @close="showCreateModal = false" />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal 
            :show="showDeleteConfirm"
            :processing="isProcessingDelete"
            title="Consign to Vault?"
            message="Are you sure you want to banish this decree to the Sunken Vault? It can be retrieved later from the forgotten archives."
            confirm-text="Banish Records"
            cancel-text="Keep Decree"
            type="danger"
            @close="showDeleteConfirm = false"
            @confirm="confirmDeletion"
        />

        <TomeLoader :show="isNavigating" message="Consulting the Scriptorium..." />
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

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

@keyframes pulse-slow {
    0%, 100% { opacity: 0.8; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.02); }
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}
</style>
