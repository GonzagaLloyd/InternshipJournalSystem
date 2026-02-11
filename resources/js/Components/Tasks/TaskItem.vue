<script setup>
import { computed } from 'vue';

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['toggle', 'delete']);

const toggle = () => emit('toggle', props.task);
const remove = () => emit('delete', props.task);

const formattedDate = computed(() => {
    if (!props.task.due_date) return '';
    return new Date(props.task.due_date).toLocaleDateString(undefined, { 
        month: 'short', 
        day: 'numeric' 
    });
});
</script>

<template>
    <div 
        @click="toggle"
        class="group relative py-6 px-6 flex items-center justify-between transition-all duration-500 cursor-pointer border-b border-white/5 hover:bg-[#353535]"
    >
        <div class="flex items-center gap-6 flex-1">
            <!-- Status Indicator -->
            <div 
                :class="[
                    task.completed 
                        ? 'bg-[#525947]/20 border-[#525947]/50 scale-90' 
                        : 'border-[#8C6A4A]/20 bg-black/40 group-hover:border-[#8C6A4A]/40',
                    'h-6 w-6 rounded-full border shrink-0 flex items-center justify-center transition-all duration-500'
                ]"
            >
                <div 
                    :class="[
                        task.completed ? 'opacity-100 scale-100' : 'opacity-0 scale-0',
                        'h-2 w-2 rounded-full bg-[#525947] shadow-[0_0_10px_rgba(82,89,71,0.5)] transition-all duration-500'
                    ]"
                ></div>
            </div>

            <div class="flex-1 min-w-0">
                <h3 
                    :class="[
                        task.completed 
                            ? 'text-[#C9B79C]/20 line-through' 
                            : 'text-[#C9B79C] font-cinzel text-lg md:text-xl tracking-wider',
                        'transition-all duration-500'
                    ]"
                >
                    {{ task.name }}
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
                </div>
            </div>
        </div>

        <!-- Action Area -->
        <div class="flex items-center gap-4">
            <!-- Delete Action -->
            <div class="opacity-100 lg:opacity-0 lg:translate-x-2 lg:group-hover:opacity-100 lg:group-hover:translate-x-0 transition-all duration-500">
                <button 
                    @click.stop="remove"
                    class="p-3 text-[#8C6A4A]/40 hover:text-[#C9B79C] hover:scale-110 transition-all active:scale-90"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
