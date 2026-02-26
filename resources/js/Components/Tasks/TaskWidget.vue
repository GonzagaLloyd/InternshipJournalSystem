<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import SkeletonLoader from '@/Components/UI/SkeletonLoader.vue';
import TaskEditModal from './TaskEditModal.vue';

const showEditModal = ref(false);
const taskToEdit = ref(null);

const openEditModal = (task) => {
    taskToEdit.value = task;
    showEditModal.value = true;
};

const props = defineProps({
    tasks: {
        type: Array,
        default: null
    }
});

const processingTaskId = ref(null);
const currentTime = ref(Date.now());
let timerInterval = null;

const startTimer = () => {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        currentTime.value = Date.now();
    }, 1000);
};

const stopTimer = () => {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = null;
};

onMounted(() => {
    if (props.tasks?.some(t => t.status === 'in_progress')) startTimer();
});

onUnmounted(() => stopTimer());

watch(() => props.tasks, (newTasks) => {
    if (newTasks?.some(t => t.status === 'in_progress')) {
        if (!timerInterval) startTimer();
    } else {
        stopTimer();
    }
}, { deep: true });

const isBacklogTask = (task) => {
    if (task.status === 'completed') return false;
    if (!task.due_date) return false;
    const due = new Date(task.due_date);
    // Include the entire due day in the grace period
    due.setHours(23, 59, 59, 999);
    return new Date() > due;
};

const groupedTasks = computed(() => {
    if (!props.tasks) return { inProgress: [], backlog: [], upcoming: [] };
    
    const priorityWeight = { high: 3, medium: 2, low: 1 };
    
    // Helper to sort tasks
    const sortTasks = (list) => {
        return [...list].sort((a, b) => {
            const weightA = priorityWeight[a.priority] || 0;
            const weightB = priorityWeight[b.priority] || 0;
            
            if (weightA !== weightB) return weightB - weightA;
            // Secondary sort: Latest created at top
            return new Date(b.created_at) - new Date(a.created_at);
        });
    };

    // Filter active tasks
    const active = props.tasks.filter(t => t.status !== 'completed');
    
    // Separate into groups
    const rawBacklog = active.filter(t => isBacklogTask(t) && t.status !== 'in_progress');
    const rawInProgress = active.filter(t => t.status === 'in_progress');
    const rawUpcoming = active.filter(t => !isBacklogTask(t) && t.status === 'todo');
    
    return { 
        inProgress: sortTasks(rawInProgress), 
        backlog: sortTasks(rawBacklog), 
        upcoming: sortTasks(rawUpcoming) 
    };
});

const taskGroups = computed(() => [
    { id: 'inProgress', label: 'Mandates In Progress', tasks: groupedTasks.value.inProgress, color: 'text-[#B07D4E]', borderColor: 'border-[#B07D4E]', bgColor: 'bg-[#B07D4E]/5' },
    { id: 'backlog', label: 'Sunken Backlog', tasks: groupedTasks.value.backlog, color: 'text-[#AF4B4B]', borderColor: 'border-[#AF4B4B]', bgColor: 'bg-[#AF4B4B]/5' },
    { id: 'upcoming', label: 'Upcoming Decrees', tasks: groupedTasks.value.upcoming, color: 'text-[#A68B6A]', borderColor: 'border-[#A68B6A]/30', bgColor: 'bg-[#2D2D2D]/40' }
].filter(group => group.tasks.length > 0));

const updateStatus = (task) => {
    const id = task.id || task._id;
    if (!id || processingTaskId.value) return;
    
    processingTaskId.value = id;
    router.patch(route('tasks.toggle', id), {}, {
        preserveScroll: true,
        onFinish: () => {
            processingTaskId.value = null;
        }
    });
};

const pauseAction = (task) => {
    const id = task.id || task._id;
    if (!id || processingTaskId.value) return;
    
    processingTaskId.value = id;
    router.patch(route('tasks.pause', id), {}, {
        preserveScroll: true,
        onFinish: () => {
            processingTaskId.value = null;
        }
    });
};
const urgencyInfo = (task) => {
    if (!task.due_date || task.status === 'completed') return null;
    
    const now = new Date();
    const due = new Date(task.due_date);
    const diffMs = due - now;
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));

    if (diffMs < 0) return { label: 'Overdue', color: 'text-[#AF4B4B]', bg: 'bg-[#AF4B4B]/10 border-[#AF4B4B]/30', glow: 'shadow-[0_0_10px_rgba(175,75,75,0.2)]' };
    if (diffMins <= 30) return { label: 'Final Moments', color: 'text-[#AF4B4B] animate-pulse-slow', bg: 'bg-[#AF4B4B]/20 border-[#AF4B4B]/50', glow: 'shadow-[0_0_15px_rgba(175,75,75,0.3)]' };
    if (diffHours < 1) return { label: 'Urgent', color: 'text-[#B07D4E]', bg: 'bg-[#B07D4E]/10 border-[#B07D4E]/30', glow: 'shadow-[0_0_12px_rgba(176,125,78,0.2)]' };
    if (diffHours < 24) return { label: 'Pending', color: 'text-[#C9B79C]/60', bg: 'bg-black/40 border-[#C9B79C]/10', glow: 'shadow-[0_0_8px_rgba(201,183,156,0.1)]' };
    
    return null;
};

const liveTimeMs = (task) => {
    const totalOld = Number(task.total_time_ms || 0);

    if (task.status !== 'in_progress' || !task.started_at) {
        return totalOld;
    }
    
    const sessionStart = new Date(task.started_at).getTime();
    if (isNaN(sessionStart)) return totalOld;
    
    // Resilience against server-client clock drift
    const elapsed = currentTime.value - sessionStart;
    return totalOld + Math.max(0, elapsed);
};

const formatLiveTime = (ms) => {
    if (!ms || ms < 0) return '0M 0S';
    const totalSecs = Math.floor(ms / 1000);
    const h = Math.floor(totalSecs / 3600);
    const m = Math.floor((totalSecs % 3600) / 60);
    const s = totalSecs % 60;
    return `${h > 0 ? h + 'H ' : ''}${m}M ${s}S`;
};

const formatDateShort = (dateString) => {
    if (!dateString) return '';
    return new Intl.DateTimeFormat('en-US', {
        month: 'short', 
        day: '2-digit'
    }).format(new Date(dateString)).toUpperCase();
};

const calculateBacklog = (created) => {
    if (!created) return '';
    const start = new Date(created);
    const now = new Date();
    const diffMs = now - start;
    const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    if (days === 0) return 'CREATED TODAY';
    return `${days}D BACKLOG`;
};

const sectionToggles = ref({
    inProgress: true,
    backlog: true,
    upcoming: true
});

const toggleSection = (section) => {
    sectionToggles.value[section] = !sectionToggles.value[section];
};
</script>

<template>
    <div class="w-full h-full flex flex-col group min-h-0">
        <div class="flex-1 flex flex-col relative overflow-hidden min-h-0">
            <!-- Header -->
            <div class="mb-6 relative z-10 px-2 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-cinzel font-bold text-cream tracking-tight">The Scriptorium</h3>
                    <p class="text-[8px] uppercase tracking-[0.3em] text-brass/40 font-bold mt-0.5">Active Mandates</p>
                </div>
                
                <button 
                    @click="router.reload()"
                    class="p-2 text-umber/30 hover:text-brass transition-all duration-500"
                    title="Refresh Ledger"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </button>
            </div>

            <!-- Task List -->
            <div class="flex-1 overflow-y-auto pr-2 scrollbar-hide space-y-8 relative z-10 pb-4">
                <template v-if="props.tasks === null">
                    <div v-for="i in 2" :key="i" class="space-y-4">
                        <SkeletonLoader width="25%" height="0.6rem" opacity="0.1" />
                        <div v-for="j in 2" :key="j" class="h-16 rounded-lg bg-white/5 animate-pulse"></div>
                    </div>
                </template>

                <template v-else>
                    <!-- Groups -->
                    <div v-for="group in taskGroups" :key="group.id" class="space-y-3">
                        <div 
                            @click="toggleSection(group.id)"
                            class="flex items-center gap-4 mb-3 px-2 opacity-60 hover:opacity-100 cursor-pointer transition-opacity group"
                            :class="group.color"
                        >
                            <span class="text-[9px] uppercase tracking-[0.3em] font-black shrink-0">{{ group.label }}</span>
                            <div class="h-[1px] flex-1 bg-current opacity-20"></div>
                            <svg 
                                :class="['w-3 h-3 transition-transform duration-300', sectionToggles[group.id] ? 'rotate-180' : '']" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <div v-show="sectionToggles[group.id]" class="flex flex-col rounded-lg overflow-hidden">
                            <div 
                                v-for="task in group.tasks" 
                                :key="task.id || task._id" 
                                @click="updateStatus(task)"
                                :class="[
                                    'group/item flex items-center gap-4 transition-all duration-300 relative py-3 px-5 border-b border-white/[0.02] last:border-b-0 cursor-pointer',
                                    processingTaskId === (task.id || task._id) ? 'opacity-40 grayscale' : '',
                                    task.priority === 'high' ? 'bg-[#AF4B4B]/10 border-l-2 border-l-[#AF4B4B]' : 
                                    (task.priority === 'medium' ? 'bg-[#B07D4E]/10 border-l-2 border-l-[#B07D4E]' : 
                                    'bg-white/[0.03] border-l-2 border-l-white/10'),
                                    task.status === 'in_progress' ? 'z-10' : ''
                                ]"
                            >
                                <!-- In-list Hover Highlight -->
                                <div class="absolute inset-0 bg-white/0 group-hover/item:bg-white/[0.04] transition-colors pointer-events-none z-0"></div>

                                <!-- Status Dot/Icon -->
                                <div class="shrink-0 relative z-10">
                                    <div class="h-6 w-6 rounded-full border border-white/10 flex items-center justify-center transition-colors group-hover/item:border-brass/30">
                                        <svg v-if="task.status === 'in_progress'" class="w-2.5 h-2.5 text-green-400" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                        <div v-else :class="[
                                            'w-1.5 h-1.5 rounded-full',
                                            task.priority === 'high' ? 'bg-[#AF4B4B]' : 
                                            (task.priority === 'medium' ? 'bg-[#B07D4E]' : 'bg-white/20')
                                        ]"></div>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0 relative z-10">
                                    <h5 class="text-sm font-serif text-cream truncate tracking-wide">
                                        {{ task.name }}
                                    </h5>
                                    
                                    <div class="flex items-center gap-3 mt-1 opacity-40">
                                        <span class="text-[8px] font-black tracking-widest uppercase">
                                            {{ group.id === 'backlog' ? calculateBacklog(task.created_at) : formatDateShort(task.created_at) }}
                                        </span>

                                        <!-- Priority Label -->
                                        <span :class="[
                                            'text-[8px] uppercase font-bold tracking-tighter',
                                            task.priority === 'high' ? 'text-[#AF4B4B]' : 
                                            (task.priority === 'medium' ? 'text-[#B07D4E]' : 'text-brass/60')
                                        ]">
                                            ● {{ task.priority || 'Routine' }}
                                        </span>

                                        <span v-if="liveTimeMs(task) > 0" class="text-[8px] font-black text-[#B07D4E] italic">
                                            {{ formatLiveTime(liveTimeMs(task)) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Subtle Action -->
                                <div class="shrink-0 flex items-center gap-1 relative z-10">
                                    <button 
                                        @click.stop="openEditModal(task)"
                                        class="p-1.5 text-umber/40 hover:text-parchment transition-colors rounded-md hover:bg-white/5"
                                        title="View Decree"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <button 
                                        v-if="task.status === 'in_progress'"
                                        @click.stop="pauseAction(task)"
                                        class="p-1.5 text-umber/40 hover:text-brass transition-colors rounded-md hover:bg-white/5"
                                        title="Pause"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty -->
                    <div v-if="taskGroups.length === 0" class="py-12 flex flex-col items-center justify-center opacity-20">
                        <span class="text-[8px] uppercase tracking-[0.5em] font-black text-brass">Silent Ledger</span>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <button 
                @click="router.visit(route('tasks.index'))"
                class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between group/link opacity-40 hover:opacity-100 transition-opacity"
            >
                <span class="text-[9px] uppercase tracking-[0.3em] font-black font-cinzel">Open Ledger</span>
                <svg class="w-3 h-3 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>
    <TaskEditModal 
        :show="showEditModal" 
        @close="showEditModal = false"  
        :task="taskToEdit"
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

/* Popping animation for the indicator */
.pop-enter-active {
    animation: bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.pop-leave-active {
    animation: bounce-in 0.2s reverse ease-in;
}

@keyframes bounce-in {
    0% { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 1s ease-out forwards;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
