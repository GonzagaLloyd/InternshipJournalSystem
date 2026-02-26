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

const pauseTask = (task) => {
    if (!task.id) return;
    processingTaskId.value = task.id;
    router.patch(route('tasks.pause', { task: task.id }), {}, {
        preserveScroll: true,
        onFinish: () => {
            processingTaskId.value = null;
        }
    });
};

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

const isBacklogTask = (task) => {
    if (task.status === 'completed') return false;
    if (!task.due_date) return false;
    const due = new Date(task.due_date);
    due.setHours(23, 59, 59, 999);
    return new Date() > due;
};

const sortedTasks = computed(() => {
    if (!props.tasks) return {
        inProgress: [],
        backlog: [],
        todo: [],
        completed: []
    };

    const tasks = {
        inProgress: [],
        backlog: [],
        todo: [],
        completed: []
    };

    const priorityWeight = { high: 3, medium: 2, low: 1 };

    props.tasks.forEach(task => {
        if (task.status === 'completed') {
            tasks.completed.push(task);
        } else if (task.status === 'in_progress') {
            tasks.inProgress.push(task);
        } else if (isBacklogTask(task)) {
            tasks.backlog.push(task);
        } else {
            tasks.todo.push(task);
        }
    });

    // Sort within each group by priority, then by creation date
    Object.keys(tasks).forEach(key => {
        tasks[key].sort((a, b) => {
            const weightA = priorityWeight[a.priority] || 0;
            const weightB = priorityWeight[b.priority] || 0;
            
            if (weightA !== weightB) return weightB - weightA;
            return new Date(b.created_at) - new Date(a.created_at);
        });
    });

    return tasks;
});

const sectionToggles = ref({
    active: true,
    overdue: true,
    upcoming: true,
    archives: false
});

const toggleSection = (section) => {
    sectionToggles.value[section] = !sectionToggles.value[section];
};
</script>

<template>
    <Head title="Scriptorium - Tasks" />

    <AuthenticatedLayout 
        title="Active <span class='text-brass'>Decrees</span>"
        subtitle="THE SCRIPTORIUM'S MANDATE"
    >
        <div class="px-6 md:px-12 lg:px-16 py-8 flex flex-col relative font-serif min-h-full">
            <div class="max-w-[1500px] mx-auto w-full flex-1 flex flex-col relative z-20">
                
                <!-- Enhanced Sticky Action Bar -->
                <div class="sticky top-20 z-[40] -mx-6 md:-mx-12 lg:-mx-16 px-6 md:px-12 lg:px-16 py-4 bg-void/95 backdrop-blur-md border-b border-white/[0.05] flex flex-col gap-6 mb-8 shadow-2xl transition-all duration-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-[0.5em] text-brass font-black">Volume Tracking</span>
                            <div class="h-[1px] w-8 bg-brass/30 mt-1"></div>
                        </div>

                        <button 
                            @click="showCreateModal = true"
                            class="group flex items-center gap-4 px-8 py-3 bg-brass hover:bg-cream rounded-sm text-void transition-all duration-500 transform active:scale-95 shadow-[0_0_20px_rgba(176,125,78,0.1)] hover:shadow-[0_0_40px_rgba(176,125,78,0.2)]"
                        >
                            <span class="text-[11px] font-black uppercase tracking-[0.4em]">Append Decree</span>
                            <div class="h-5 w-5 rounded-full bg-void/10 flex items-center justify-center group-hover:bg-void/20 transition-colors">
                                <svg class="w-3.5 h-3.5 text-void" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 6v12m6-6H6" /></svg>
                            </div>
                        </button>
                    </div>

                    <!-- Spreadsheet Align Headers -->
                    <div class="hidden lg:grid grid-cols-12 gap-4 px-12 opacity-20">
                        <div class="col-span-5 text-[9px] uppercase tracking-[0.8em] font-black">Decree Identifier</div>
                        <div class="col-span-2 text-[9px] uppercase tracking-[0.8em] font-black text-center">Urgency</div>
                        <div class="col-span-3 text-[9px] uppercase tracking-[0.8em] font-black text-center">Ritual State</div>
                        <div class="col-span-2 text-[9px] uppercase tracking-[0.8em] font-black text-right pr-4">Seal Date</div>
                    </div>
                </div>

                <!-- Simplified Task Volumes -->
                <div class="flex-1 space-y-12">
                    <!-- Loading State -->
                    <template v-if="props.tasks === null">
                        <div class="space-y-4">
                            <div v-for="i in 10" :key="i" class="h-16 bg-white/[0.01] border-b border-white/[0.02] animate-pulse"></div>
                        </div>
                    </template>

                    <template v-else>
                        <!-- Section: Active -->
                        <div v-if="sortedTasks.inProgress.length > 0">
                            <div 
                                @click="toggleSection('active')"
                                class="flex items-center gap-4 mb-4 px-6 opacity-40 hover:opacity-100 cursor-pointer transition-opacity group"
                            >
                                <span class="text-[9px] uppercase tracking-[0.5em] font-black text-brass">Active Mandates</span>
                                <div class="h-[1px] flex-1 bg-brass/20"></div>
                                <svg 
                                    :class="['w-3 h-3 transition-transform duration-300', sectionToggles.active ? 'rotate-180' : '']" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <div v-show="sectionToggles.active" class="flex flex-col rounded-lg overflow-hidden">
                                <TaskItem 
                                    v-for="task in sortedTasks.inProgress" 
                                    :key="task.id"
                                    :task="task"
                                    :processing="processingTaskId === task.id"
                                    @toggle="toggleTask"
                                    @delete="deleteTask"
                                    @pause="pauseTask"
                                />
                            </div>
                        </div>

                        <!-- Section: Overdue -->
                        <div v-if="sortedTasks.backlog.length > 0">
                            <div 
                                @click="toggleSection('overdue')"
                                class="flex items-center gap-4 mb-4 px-6 opacity-40 hover:opacity-100 cursor-pointer transition-opacity group"
                            >
                                <span class="text-[9px] uppercase tracking-[0.5em] font-black text-red-500">Overdue Ordinances</span>
                                <div class="h-[1px] flex-1 bg-red-500/20"></div>
                                <svg 
                                    :class="['w-3 h-3 transition-transform duration-300', sectionToggles.overdue ? 'rotate-180' : '']" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <div v-show="sectionToggles.overdue" class="flex flex-col rounded-lg overflow-hidden">
                                <TaskItem 
                                    v-for="task in sortedTasks.backlog" 
                                    :key="task.id"
                                    :task="task"
                                    :processing="processingTaskId === task.id"
                                    @toggle="toggleTask"
                                    @delete="deleteTask"
                                    @pause="pauseTask"
                                />
                            </div>
                        </div>

                        <!-- Section: Upcoming -->
                        <div v-if="sortedTasks.todo.length > 0">
                            <div 
                                @click="toggleSection('upcoming')"
                                class="flex items-center gap-4 mb-4 px-6 opacity-40 hover:opacity-100 cursor-pointer transition-opacity group"
                            >
                                <span class="text-[9px] uppercase tracking-[0.5em] font-black text-parchment">Upcoming Decrees</span>
                                <div class="h-[1px] flex-1 bg-white/10"></div>
                                <svg 
                                    :class="['w-3 h-3 transition-transform duration-300', sectionToggles.upcoming ? 'rotate-180' : '']" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <div v-show="sectionToggles.upcoming" class="flex flex-col rounded-lg overflow-hidden">
                                <TaskItem 
                                    v-for="task in sortedTasks.todo" 
                                    :key="task.id"
                                    :task="task"
                                    :processing="processingTaskId === task.id"
                                    @toggle="toggleTask"
                                    @delete="deleteTask"
                                    @pause="pauseTask"
                                />
                            </div>
                        </div>

                        <!-- Section: Completed -->
                        <div v-if="sortedTasks.completed.length > 0" class="pb-20">
                            <div 
                                @click="toggleSection('archives')"
                                class="flex items-center gap-4 mb-4 px-6 opacity-30 hover:opacity-100 cursor-pointer transition-all group"
                            >
                                <span class="text-[9px] uppercase tracking-[0.5em] font-black">Fulfilled Records</span>
                                <div class="h-[1px] flex-1 bg-white/5"></div>
                                <svg 
                                    :class="['w-3 h-3 transition-transform duration-300', sectionToggles.archives ? 'rotate-180' : '']" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <div v-show="sectionToggles.archives" class="flex flex-col rounded-lg overflow-hidden opacity-40 hover:opacity-100 transition-opacity">
                                <TaskItem 
                                    v-for="task in sortedTasks.completed" 
                                    :key="task.id"
                                    :task="task"
                                    :processing="processingTaskId === task.id"
                                    @toggle="toggleTask"
                                    @delete="deleteTask"
                                    @pause="pauseTask"
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
