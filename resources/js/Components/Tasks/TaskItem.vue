<script setup>
import { ref, computed } from 'vue';
import TaskEditModal from './TaskEditModal.vue';

const props = defineProps({
    task: {
        type: Object,
        required: true
    },
    processing: {
        type: Boolean,
        default: false
    }
});

const showEditModal = ref(false);
const taskToEdit = ref(null); 
const currentTime = ref(Date.now());
let timerInterval = null;

const emit = defineEmits(['toggle', 'delete', 'pause']);

const toggle = () => emit('toggle', props.task);
const remove = () => emit('delete', props.task);
const pauseTask = () => emit('pause', props.task);

// Live timer for In Progress task
const liveTimeMs = computed(() => {
    const totalOld = Number(props.task.total_time_ms || 0);
    
    if (props.task.status !== 'in_progress' || !props.task.started_at) {
        return totalOld;
    }
    
    const sessionStart = new Date(props.task.started_at).getTime();
    if (isNaN(sessionStart)) return totalOld;
    
    // Calculate current session drift
    const elapsed = currentTime.value - sessionStart;
    
    // Resilience against server-client clock drift (never show negative time in current session)
    return totalOld + Math.max(0, elapsed);
});

const formatLiveTime = (ms) => {
    if (!ms || ms < 0) return '0M 0S';
    const totalSecs = Math.floor(ms / 1000);
    const h = Math.floor(totalSecs / 3600);
    const m = Math.floor((totalSecs % 3600) / 60);
    const s = totalSecs % 60;
    return `${h > 0 ? h + 'H ' : ''}${m}M ${s}S`;
};

// Backlog detection
const isBacklog = computed(() => {
    if (props.task.status === 'completed') return false;
    if (!props.task.due_date) return false;
    const due = new Date(props.task.due_date);
    due.setHours(23, 59, 59, 999); // End of due day
    return new Date() > due;
});

const currentStatusLabel = computed(() => {
    if (props.task.status === 'completed') return 'FULFILLED';
    if (isBacklog.value) return 'BACKLOG';
    if (props.task.status === 'in_progress') return 'IN PROGRESS';
    return 'TO DO';
});

import { onMounted, onUnmounted, watch } from 'vue';

const startTimer = () => {
    if (timerInterval) clearInterval(timerInterval);
    // Force immediate sync update
    currentTime.value = Date.now();
    timerInterval = setInterval(() => {
        currentTime.value = Date.now();
    }, 1000);
};

const stopTimer = () => {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = null;
};

onMounted(() => {
    if (props.task.status === 'in_progress') startTimer();
});

onUnmounted(() => stopTimer());

watch(() => props.task.status, (newStatus) => {
    if (newStatus === 'in_progress') {
        startTimer();
    } else {
        stopTimer();
    }
}, { immediate: true });

const formattedDate = computed(() => {
    if (!props.task.due_date) return '';
    const date = new Date(props.task.due_date);
    return date.toLocaleDateString(undefined, { 
        month: 'short', 
        day: 'numeric' 
    });
});

const urgencyInfo = computed(() => {
    if (!props.task.due_date || props.task.status === 'completed') return null;
    
    const now = new Date();
    const due = new Date(props.task.due_date);
    const diffMs = due - now;
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));

    if (diffMs < 0) return { label: 'Overdue', color: 'text-[#AF4B4B]', bg: 'bg-[#AF4B4B]/10 border-[#AF4B4B]/30', glow: 'shadow-[0_0_10px_rgba(175,75,75,0.2)]', level: 'past' };
    if (diffMins <= 30) return { label: 'Final Moments', color: 'text-[#AF4B4B] animate-pulse-slow', bg: 'bg-[#AF4B4B]/20 border-[#AF4B4B]/50', glow: 'shadow-[0_0_15px_rgba(175,75,75,0.3)]', level: 'critical' };
    if (diffHours < 1) return { label: 'Urgent', color: 'text-[#B07D4E]', bg: 'bg-[#B07D4E]/10 border-[#B07D4E]/30', glow: 'shadow-[0_0_12px_rgba(176,125,78,0.2)]', level: 'urgent' };
    if (diffHours < 24) return { label: 'Pending', color: 'text-[#C9B79C]/60', bg: 'bg-black/40 border-[#C9B79C]/10', glow: 'shadow-[0_0_8px_rgba(201,183,156,0.1)]', level: 'warning' };
    
    return null;
});

const openEditModal = (task) => {
    showEditModal.value = true;
    taskToEdit.value = task;
}

const formatDateShort = (dateString) => {
    if (!dateString) return '';
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: '2-digit'
    }).format(new Date(dateString)).toUpperCase();
};

const calculateDuration = (created, completed) => {
    if (!created || !completed) return '';
    const start = new Date(created);
    const end = new Date(completed);
    const diffMs = end - start;
    
    if (diffMs < 0) return 'INSTANT';
    
    const mins = Math.floor(diffMs / (1000 * 60));
    if (mins < 60) return `${mins}M`;
    
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${hours}H ${mins % 60}M`;
    
    const days = Math.floor(hours / 24);
    return `${days}D ${hours % 24}H`;
};

const calculateBacklog = (created) => {
    if (!created) return '';
    const start = new Date(created);
    const now = new Date();
    const diffMs = now - start;
    
    const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    if (days === 0) return 'TODAY';
    return `${days}D BACKLOG`;
};
</script>

<template>
    <div 
        @click="toggle"
        :class="[
            'group relative border-b border-white/[0.02] last:border-b-0 transition-all duration-300 cursor-pointer overflow-hidden',
            task.status === 'completed' ? 'opacity-30 grayscale' : '',
            task.priority === 'high' ? 'bg-[#AF4B4B]/10 border-l-2 border-l-[#AF4B4B]' : 
            task.priority === 'medium' ? 'bg-[#B07D4E]/10 border-l-2 border-l-[#B07D4E]' : 
            'bg-white/[0.03] border-l-2 border-l-white/10',
            task.status === 'in_progress' ? 'z-10' : '',
            processing ? 'opacity-40 grayscale' : '',
            'py-3'
        ]"
    >
        <!-- In-list Hover Highlight -->
        <div class="absolute inset-0 bg-white/0 group-hover:bg-white/[0.04] transition-colors pointer-events-none z-0"></div>
        <!-- Spreadsheet Grid Alignment (Preserved for Column Tracking) -->
        <div class="relative z-10 flex flex-col lg:grid lg:grid-cols-12 lg:items-center gap-2 lg:gap-4 px-4 sm:px-6 lg:px-10 py-2 lg:py-0">
            <!-- Col 1-5: Decree Identity & Hierarchy -->
            <div class="lg:col-span-5 min-w-0 flex items-center gap-4 lg:gap-8 w-full">
                <!-- Circular Status Well -->
                <div class="relative shrink-0 flex items-center justify-center h-8 w-8 md:h-10 md:w-10 rounded-full bg-void/60 border border-white/10 shadow-inner">
                    <div 
                        :class="[
                            'h-2 w-2 md:h-2.5 md:w-2.5 rounded-full transition-all duration-700',
                            task.status === 'completed' ? 'bg-parchment/20' : 
                            (task.status === 'in_progress' ? 'bg-[#4ADE80] shadow-[0_0_12px_#4ADE80]' : 
                             (task.priority === 'high' ? 'bg-[#AF4B4B]' : 
                              (task.priority === 'medium' ? 'bg-[#B07D4E]' : 'bg-parchment/30')))
                        ]"
                    ></div>
                    <div v-if="task.status === 'in_progress'" class="absolute inset-0 border border-[#4ADE80]/20 rounded-full animate-pulse"></div>
                </div>

                <div class="flex flex-col min-w-0 flex-1">
                    <h3 
                        :class="[
                            task.status === 'completed' ? 'text-parchment/30 line-through' : 'text-cream',
                            'font-serif text-[15px] md:text-lg font-medium tracking-tight truncate transition-colors'
                        ]"
                    >
                        {{ task.name }}
                    </h3>
                </div>
            </div>

            <!-- Col 6-7: Urgency Column -->
            <div class="hidden lg:flex col-span-2 items-center justify-center">
                <span :class="[
                    'text-[10px] uppercase tracking-[0.3em] font-black opacity-40 transition-opacity group-hover:opacity-100',
                    task.priority === 'high' ? 'text-[#AF4B4B]' : (task.priority === 'medium' ? 'text-[#B07D4E]' : 'text-parchment')
                ]">
                    {{ task.priority || 'Routine' }}
                </span>
            </div>

            <!-- Bottom Mobile Row / Remaining Desktop Columns -->
            <div class="flex items-center justify-between w-full lg:contents pl-12 lg:pl-0">
                <!-- Col 8-10: Ritual State Column -->
                <div class="lg:col-span-3 flex items-center lg:flex-col lg:items-center gap-2 lg:gap-0">
                    <!-- Mobile Priority Label -->
                    <span class="lg:hidden text-[8px] uppercase tracking-[0.2em] font-black" :class="task.priority === 'high' ? 'text-[#AF4B4B]' : (task.priority === 'medium' ? 'text-[#B07D4E]' : 'text-parchment/50')">
                        {{ task.priority || 'Routine' }} 
                        <span class="opacity-30 mx-0.5">•</span>
                    </span>

                    <span :class="[
                        'text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-black',
                        task.status === 'in_progress' ? 'text-brass' : 'text-parchment/20'
                    ]">
                        {{ currentStatusLabel }}
                    </span>
                    <span v-if="liveTimeMs > 0" class="text-[8px] md:text-[8px] font-sans font-black italic text-brass/40 transition-colors group-hover:text-brass/60">
                        {{ formatLiveTime(liveTimeMs) }}
                    </span>
                </div>

                <!-- Col 11-12: Seal Date / Action Blur Area -->
                <div class="lg:col-span-2 flex items-center justify-end lg:pr-6 relative">
                    <!-- Date Content (Blurs on Hover on Desktop) -->
                    <div class="hidden sm:block transition-all duration-500 lg:group-hover:blur-[4px] lg:group-hover:opacity-0 lg:group-hover:scale-110">
                        <span :class="['text-[9px] md:text-[11px] uppercase tracking-tighter font-black', isBacklog ? 'text-red-500/60' : 'text-parchment/20']">
                            {{ formattedDate }}
                        </span>
                    </div>

                    <!-- Focused Action Cluster (Emerges on Hover/Focus on Desktop, Always Visible on Mobile) -->
                    <div class="lg:absolute lg:inset-0 flex items-center justify-end opacity-100 lg:opacity-0 lg:group-hover:opacity-100 focus-within:opacity-100 transition-all duration-300 lg:scale-90 lg:group-hover:scale-100 pr-0 lg:pr-4 ml-3 lg:ml-0">
                        <div class="flex items-center gap-1 bg-void/90 backdrop-blur-xl rounded-lg border border-white/10 shadow-3xl">
                            <button 
                                v-if="task.status === 'completed' || task.status === 'in_progress'"
                                @click.stop="task.status === 'completed' ? toggle() : pauseTask()"
                                class="p-2 lg:p-1.5 text-parchment/40 hover:text-brass transition-all hover:bg-white/5 rounded-md"
                            >
                                <svg v-if="task.status === 'completed'" class="w-4 h-4 lg:w-3.5 lg:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                <svg v-else class="w-4 h-4 lg:w-3.5 lg:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </button>

                            <button 
                                @click.stop="openEditModal(task)"
                                class="p-2 lg:p-1.5 text-parchment/40 hover:text-parchment transition-all hover:bg-white/5 rounded-md"
                                title="View Decree"
                            >
                                <svg class="w-4 h-4 lg:w-3.5 lg:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>

                            <button 
                                @click.stop="remove"
                                class="p-2 lg:p-1.5 text-parchment/20 hover:text-red-500 transition-all hover:bg-red-500/5 rounded-md"
                            >
                                <svg class="w-4 h-4 lg:w-3.5 lg:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <TaskEditModal 
        :show="showEditModal" 
        @close="showEditModal = false"  
        :task="taskToEdit"
    />
    
</template>

<style scoped>
@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.7; }
}

.pulse-active {
    animation: pulse-slow 3s ease-in-out infinite;
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

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
