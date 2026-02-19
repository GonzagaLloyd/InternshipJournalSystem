<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    tasks: Array
});

const processingTaskId = ref(null);

const sortedTasks = computed(() => {
    if (!props.tasks) return [];
    return [...props.tasks].sort((a, b) => {
        if (a.completed !== b.completed) {
            return a.completed ? 1 : -1;
        }
        return new Date(b.created_at || 0) - new Date(a.created_at || 0);
    });
});

const urgencyInfo = (task) => {
    if (!task.due_date || task.completed) return null;
    
    const now = new Date();
    const due = new Date(task.due_date);
    const diffMs = due - now;
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));

    if (diffMs < 0) return { color: 'text-[#AF4B4B]', glow: 'shadow-[0_0_10px_rgba(175,75,75,0.2)]' };
    if (diffMins <= 30) return { color: 'text-[#AF4B4B] animate-pulse', glow: 'shadow-[0_0_15px_rgba(175,75,75,0.4)]' };
    if (diffHours < 1) return { color: 'text-[#B07D4E]', glow: 'shadow-[0_0_12px_rgba(176,125,78,0.3)]' };
    if (diffHours < 24) return { color: 'text-yellow-500/80', glow: 'shadow-[0_0_8px_rgba(234,179,8,0.15)]' };
    
    return null;
};

const toggleTask = (task) => {
    // Use task.id (which we aliased to _id in the model)
    const id = task.id || task._id;
    processingTaskId.value = id;
    
    router.patch(route('tasks.toggle', id), {}, {
        preserveScroll: true,
        onFinish: () => {
            processingTaskId.value = null;
        }
    });
};
</script>

<template>
    <div class="w-full h-full flex flex-col group min-h-0">
        <div class="flex-1 flex flex-col relative overflow-hidden min-h-0">
            <!-- Header -->
            <div class="mb-6 relative z-10">
                <p class="text-[10px] uppercase tracking-[0.4em] text-brass font-bold mb-1">Status</p>
                <h3 class="text-2xl font-cinzel font-bold text-cream tracking-tight">Active Tasks</h3>
            </div>

            <!-- Task List with Lineage -->
            <div class="flex-1 overflow-y-auto pr-2 md:pr-4 scrollbar-hide space-y-6 md:space-y-8 relative z-10 pb-6">
                <!-- Subtle Vertical Line -->
                <div class="absolute left-2.5 top-2 bottom-6 w-[1px] bg-gradient-to-b from-umber/20 via-umber/5 to-transparent"></div>

                <div 
                    v-for="task in sortedTasks" 
                    :key="task.id || task._id" 
                    :class="[
                        'group/item flex items-start gap-5 md:gap-7 cursor-pointer select-none transition-all duration-500 relative py-4 px-2 rounded-xl hover:bg-white/[0.03]',
                        processingTaskId === (task.id || task._id) ? 'bg-umber/5' : ''
                    ]"
                    @click="toggleTask(task)"
                >
                    <!-- Processing Glow -->
                    <Transition name="fade">
                        <div v-if="processingTaskId === (task.id || task._id)" class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-umber to-transparent shadow-[0_0_15px_var(--color-accent-umber)] z-20"></div>
                    </Transition>
                    <Transition name="fade">
                        <div v-if="processingTaskId === (task.id || task._id)" class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-umber to-transparent shadow-[0_0_15px_var(--color-accent-umber)] z-20"></div>
                    </Transition>

                    <!-- Simple Checkbox / Node -->
                    <div 
                        :class="task.completed 
                            ? 'bg-umber border-umber shadow-[0_0_15px_rgba(140,106,74,0.3)]' 
                            : 'border-brass/60 bg-black/60 shadow-[0_0_10px_rgba(140,106,74,0.1)] group-hover/item:scale-110 group-hover/item:border-cream group-hover/item:shadow-[0_0_20px_rgba(140,106,74,0.3)]'"
                        class="h-6 w-6 rounded-full border-2 shrink-0 flex items-center justify-center transition-all duration-300 z-10 mt-1 active:scale-90"
                    >
                        <Transition name="pop" mode="out-in">
                            <div 
                                v-if="task.completed"
                                key="completed"
                                class="text-void scale-110"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div 
                                v-else
                                key="active"
                                class="h-2 w-2 rounded-full bg-brass animate-pulse shadow-[0_0_8px_var(--color-accent-brass)]"
                            ></div>
                        </Transition>
                    </div>

                    <!-- Text & Priority -->
                    <div class="flex flex-col gap-1.5 min-w-0">
                        <span 
                            :class="[
                                task.completed 
                                    ? 'text-cream/30 italic font-normal' 
                                    : (urgencyInfo(task) ? urgencyInfo(task).color + ' font-bold' : 'text-cream group-hover/item:text-white font-semibold'),
                                'text-lg md:text-[20px] tracking-wide transition-all duration-500 font-serif relative inline-block'
                            ]"
                        >
                            {{ task.name }}
                            <!-- Ink Stroke (Thematic Strike-through) -->
                            <div 
                                :class="task.completed ? 'w-full opacity-60 scale-x-100' : 'w-0 opacity-0 scale-x-0'"
                                class="absolute top-[55%] left-0 h-[2px] bg-umber transition-all duration-700 ease-in-out origin-left pointer-events-none rounded-full"
                                style="mask-image: linear-gradient(to right, black, transparent);"
                            ></div>
                        </span>
                        <div class="flex items-center gap-4">
                            <span v-if="!task.completed" class="text-[11px] uppercase tracking-[0.2em] text-brass font-bold font-serif group-hover/item:text-cream transition-all duration-500">
                                {{ task.priority || 'Medium' }}
                            </span>
                            <span v-if="urgencyInfo(task)" class="text-[9px] uppercase tracking-[0.15em] font-black font-serif px-2 py-0.5 rounded shadow-sm bg-black/60 border border-white/[0.05]" :class="urgencyInfo(task).color">
                                Due Shortly
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Empty State (Refined) -->
                <div v-if="sortedTasks.length === 0" class="flex-1 flex flex-col items-center justify-center py-20 opacity-0 animate-fade-in group-hover:opacity-100 transition-opacity duration-1000">
                    <div class="relative mb-6">
                        <svg class="w-16 h-16 text-umber/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <div class="absolute inset-0 bg-umber/5 blur-xl rounded-full animate-pulse"></div>
                    </div>
                    <span class="text-[9px] uppercase tracking-[0.5em] font-black text-brass italic">The Scriptorium is Silent</span>
                    <p class="text-[8px] font-serif text-umber/50 mt-2 tracking-[0.1em]">No divine decrees await transcription</p>
                </div>
            </div>

            <!-- Refined Button -->
            <button 
                @click="router.visit(route('tasks.index'))"
                class="mt-auto group/btn flex items-center gap-5 py-6 border-t border-white/[0.03] text-umber/40 hover:text-parchment transition-all"
            >
                <div class="h-10 w-10 rounded-full border border-umber/20 flex items-center justify-center group-hover/btn:border-umber/60 group-hover/btn:bg-umber/5 transition-all">
                    <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </div>
                <div class="text-left">
                    <span class="text-[10px] font-cinzel tracking-[0.3em] uppercase text-umber group-hover/btn:text-parchment transition-colors">Open Ledger</span>
                    <p class="text-[9px] font-serif italic text-white/30 group-hover/btn:text-white/50 mt-0.5 transition-colors">View all divine decrees</p>
                </div>
            </button>
        </div>
    </div>
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
