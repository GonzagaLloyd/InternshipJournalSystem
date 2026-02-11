<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
    tasks: Array
});

const toggleTask = (task) => {
    // Use task.id (which we aliased to _id in the model)
    const id = task.id || task._id;
    router.patch(route('tasks.toggle', id), {}, {
        preserveScroll: true
    });
};
</script>

<template>
    <div class="w-full h-full flex flex-col group">
        <div class="flex-1 flex flex-col relative overflow-hidden">
            <!-- Header -->
            <div class="mb-10 relative z-10">
                <p class="text-[9px] uppercase tracking-[0.4em] text-[#8C6A4A] font-black mb-2 opacity-80">Status</p>
                <h3 class="text-3xl font-cinzel text-[#C9B79C] tracking-tight">Active Tasks</h3>
            </div>

            <!-- Task List with Lineage -->
            <div class="flex-1 overflow-y-auto pr-4 scrollbar-hide space-y-7 relative z-10 pb-6">
                <!-- Vertical Line -->
                <div class="absolute left-2 top-2 bottom-6 w-[1px] bg-gradient-to-b from-[#8C6A4A]/40 via-[#8C6A4A]/10 to-transparent"></div>

                <div 
                    v-for="task in tasks" 
                    :key="task.id || task._id" 
                    class="group/item flex items-start gap-6 cursor-pointer select-none transition-all duration-500 relative" 
                    @click="toggleTask(task)"
                >
                    <!-- Simple Checkbox / Node -->
                    <div 
                        :class="task.completed 
                            ? 'bg-[#525947] border-[#525947] shadow-[0_0_10px_rgba(82,89,71,0.3)]' 
                            : 'border-[#8C6A4A]/30 bg-[#1B1B1B] group-hover/item:border-[#8C6A4A] group-hover/item:shadow-[0_0_10px_rgba(140,106,74,0.2)]'"
                        class="h-4 w-4 rounded-full border shrink-0 flex items-center justify-center transition-all duration-500 z-10 mt-1"
                    >
                        <div v-if="task.completed" class="h-1 w-1 rounded-full bg-[#C9B79C]"></div>
                        <!-- Hover Pulse -->
                        <div v-if="!task.completed" class="absolute inset-0 rounded-full border border-[#8C6A4A]/0 group-hover/item:border-[#8C6A4A]/40 group-hover/item:animate-ping opacity-0 group-hover/item:opacity-100 transition-all duration-700"></div>
                    </div>

                    <!-- Text & Priority -->
                    <div class="flex flex-col gap-1 min-w-0">
                        <span 
                            :class="[
                                task.completed 
                                    ? 'text-[#C9B79C]/20 line-through italic' 
                                    : 'text-[#C9B79C]/70 group-hover/item:text-[#C9B79C]',
                                'text-[15px] font-medium leading-tight transition-all duration-300 font-serif'
                            ]"
                        >
                            {{ task.name }}
                        </span>
                        <span v-if="!task.completed" class="text-[8px] uppercase tracking-[0.2em] text-[#8C6A4A]/40 font-black font-serif group-hover/item:text-[#8C6A4A]/60 transition-colors">
                            {{ task.priority || 'Medium' }}
                        </span>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="tasks && tasks.length === 0" class="flex flex-col items-center justify-center py-12 opacity-10">
                    <span class="text-[9px] uppercase tracking-[0.4em] font-black text-[#C9B79C]">Quiet in the Scriptorium</span>
                </div>
            </div>

            <!-- Refined Button -->
            <button 
                @click="router.visit(route('tasks.index'))"
                class="mt-auto group/btn flex items-center gap-5 py-8 border-t border-white/[0.03] text-[#8C6A4A]/40 hover:text-[#C9B79C] transition-all"
            >
                <div class="h-11 w-11 rounded-full border border-[#8C6A4A]/20 flex items-center justify-center group-hover/btn:border-[#8C6A4A]/60 group-hover/btn:bg-[#8C6A4A]/5 transition-all">
                    <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </div>
                <div class="text-left">
                    <span class="text-[10px] font-cinzel tracking-[0.3em] uppercase text-[#8C6A4A] group-hover/btn:text-[#C9B79C] transition-colors">Open Ledger</span>
                    <p class="text-[8px] font-serif italic text-white/10 group-hover/btn:text-white/20 mt-1 transition-colors">View all divine decrees</p>
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
</style>
