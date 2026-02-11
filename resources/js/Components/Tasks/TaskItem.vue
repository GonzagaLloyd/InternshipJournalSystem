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

const emit = defineEmits(['toggle', 'delete']);

const toggle = () => emit('toggle', props.task);
const remove = () => emit('delete', props.task);

const formattedDate = computed(() => {
    if (!props.task.due_date) return '';
    const date = new Date(props.task.due_date);
    return date.toLocaleDateString(undefined, { 
        month: 'short', 
        day: 'numeric' 
    });
});

const urgencyInfo = computed(() => {
    if (!props.task.due_date || props.task.completed) return null;
    
    const now = new Date();
    const due = new Date(props.task.due_date);
    const diffMs = due - now;
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));

    if (diffMs < 0) return { label: 'Overdue', color: 'text-red-500', glow: 'shadow-[0_0_15px_rgba(239,68,68,0.3)]', level: 'past' };
    if (diffMins <= 30) return { label: 'Final Moments', color: 'text-red-500 animate-pulse', glow: 'shadow-[0_0_25px_rgba(239,68,68,0.5)]', level: 'critical' };
    if (diffHours < 1) return { label: 'Less than an hour', color: 'text-orange-500', glow: 'shadow-[0_0_20px_rgba(249,115,22,0.4)]', level: 'urgent' };
    if (diffHours < 24) return { label: 'Due tomorrow', color: 'text-yellow-500/80', glow: 'shadow-[0_0_15px_rgba(234,179,8,0.2)]', level: 'warning' };
    
    return null;
});

const openEditModal = (task) => {
    showEditModal.value = true;
    taskToEdit.value = task;

}
</script>

<template>
    <div 
        @click="toggle"
        :class="[
            task.completed 
                ? 'opacity-60 grayscale-[0.3]' 
                : 'bg-[#2D2D2D]/40 border-[#8C6A4A]/5 shadow-[inset_0_0_40px_rgba(0,0,0,0.2)]',
            processing ? 'bg-[#8C6A4A]/5' : '',
            !task.completed && urgencyInfo ? urgencyInfo.glow : '',
            'group relative py-8 px-8 flex items-center justify-between transition-all duration-700 cursor-pointer border-b border-white/5 hover:bg-[#353535]'
        ]"
    >
        <!-- Top & Bottom Glow Dividers (Processing State) -->
        <Transition name="fade">
            <div v-if="processing" class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-[#8C6A4A] to-transparent shadow-[0_0_20px_#8C6A4A] z-30"></div>
        </Transition>
        <Transition name="fade">
            <div v-if="processing" class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-[#8C6A4A] to-transparent shadow-[0_0_20px_#8C6A4A] z-30"></div>
        </Transition>

        <div class="flex items-center gap-6 flex-1">
            <!-- Status Indicator -->
            <div 
                :class="[
                    task.completed 
                        ? 'bg-[#8C6A4A] border-[#8C6A4A] shadow-[0_0_20px_rgba(140,106,74,0.4)]' 
                        : 'border-[#8C6A4A] bg-black/60 shadow-[0_0_15px_rgba(140,106,74,0.15)] pulse-active',
                    'h-8 w-8 rounded-full border-2 shrink-0 flex items-center justify-center transition-all duration-300 relative overflow-hidden active:scale-90 hover:scale-105'
                ]"
            >
                <Transition name="pop" mode="out-in">
                    <div 
                        v-if="task.completed"
                        key="completed"
                        class="text-[#1B1B1B] scale-110"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div 
                        v-else
                        key="active"
                        class="w-1.5 h-1.5 rounded-full bg-[#8C6A4A] shadow-[0_0_10px_#8C6A4A]"
                    ></div>
                </Transition>
            </div>

            <div class="flex-1 min-w-0">
                <h3 
                    :class="[
                        task.completed 
                            ? 'text-[#C9B79C]/20' 
                            : 'text-[#C9B79C] drop-shadow-[0_2px_10px_rgba(0,0,0,0.5)]',
                        'font-cinzel text-xl md:text-2xl tracking-[0.05em] transition-all duration-500 relative inline-block'
                    ]"
                >
                    {{ task.name }}
                    <!-- Strike Line Animation -->
                    <div 
                        :class="task.completed ? 'w-[105%] opacity-100' : 'w-0 opacity-0'"
                        class="absolute top-[55%] -left-1 h-[2.5px] bg-[#8C6A4A] transition-all duration-500 ease-[cubic-bezier(0.65,0,0.35,1)] pointer-events-none rounded-full shadow-[0_0_10px_rgba(140,106,74,0.5)]"
                    ></div>
                </h3>
                
                <div class="flex items-center gap-6 mt-1.5">
                    <!-- Priority -->
                    <div :class="[
                        task.completed ? 'opacity-20' : 'opacity-80',
                        task.priority === 'high' ? 'text-[#8C6A4A]' : 
                        task.priority === 'medium' ? 'text-[#C9B79C]/60' : 'text-[#8C6A4A]/40',
                        'text-[10px] uppercase tracking-[0.3em] font-black font-serif'
                    ]">
                        {{ task.priority || 'medium' }}
                    </div>

                    <!-- Due Date -->
                    <div v-if="task.due_date" 
                        :class="[
                            task.completed ? 'opacity-20' : 'opacity-40 text-[#C9B79C]',
                            'flex items-center gap-2 font-serif'
                        ]"
                    >
                        <span class="text-[10px] uppercase tracking-[0.2em] font-black">
                            {{ formattedDate }}
                        </span>
                    </div>

                    <!-- Urgency Badge -->
                    <Transition name="fade">
                        <div v-if="urgencyInfo" 
                            :class="[
                                urgencyInfo.color,
                                'text-[9px] uppercase tracking-[0.2em] font-black border px-2 py-0.5 rounded-full bg-black/40'
                            ]"
                            style="border-color: currentColor;"
                        >
                            {{ urgencyInfo.label }}
                        </div>
                    </Transition>
                </div>
            </div>
        </div>

        <!-- Action Area -->
        <div class="flex items-center gap-2">
            <!-- Edit Action -->
            <div class="opacity-100 lg:opacity-0 lg:translate-x-2 lg:group-hover:opacity-100 lg:group-hover:translate-x-0 transition-all duration-300">
                <button 
                    @click.stop="openEditModal(task)"
                    class="p-3 text-[#C9B79C]/40 hover:text-[#C9B79C] hover:scale-110 transition-all active:scale-90"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </div>

            <!-- Delete Action -->
            <div class="opacity-100 lg:opacity-0 lg:translate-x-2 lg:group-hover:opacity-100 lg:group-hover:translate-x-0 transition-all duration-500 delay-75">
                <button 
                    @click.stop="remove"
                    class="p-3 text-[#8C6A4A]/40 hover:text-red-400 hover:scale-110 transition-all active:scale-90"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
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
