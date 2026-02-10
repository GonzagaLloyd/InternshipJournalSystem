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
                <p class="text-[9px] uppercase tracking-[0.4em] text-[#8b2635] font-black mb-2 opacity-60">Status</p>
                <h3 class="text-3xl font-cinzel text-[#f4e4bc] tracking-tight">Active Tasks</h3>
            </div>

            <!-- Task List -->
            <div class="flex-1 overflow-y-auto pr-4 scrollbar-hide space-y-6 relative z-10 pb-6">
                <div 
                    v-for="task in tasks" 
                    :key="task.id || task._id" 
                    class="group/item flex items-center gap-5 cursor-pointer select-none transition-all duration-300" 
                    @click="toggleTask(task)"
                >
                    <!-- Simple Checkbox -->
                    <div 
                        :class="task.completed 
                            ? 'bg-[#8b2635] border-[#8b2635]' 
                            : 'border-white/10 bg-white/[0.02] group-hover/item:border-[#d9c5a3]/40'"
                        class="h-5 w-5 rounded-full border shrink-0 flex items-center justify-center transition-all duration-500"
                    >
                        <div v-if="task.completed" class="h-1.5 w-1.5 rounded-full bg-[#f4e4bc]"></div>
                    </div>

                    <!-- Text -->
                    <span 
                        :class="[
                            task.completed 
                                ? 'text-[#8c7e6a]/40 line-through italic' 
                                : 'text-[#d9c5a3] group-hover/item:text-[#f4e4bc]',
                            'text-base font-medium leading-relaxed transition-all duration-300'
                        ]"
                    >
                        {{ task.name }}
                    </span>
                </div>

                <!-- Empty State -->
                <div v-if="tasks && tasks.length === 0" class="flex flex-col items-center justify-center py-12 opacity-20">
                    <span class="text-[9px] uppercase tracking-[0.4em] font-black text-[#8c7e6a]">Quiet in the Scriptorium</span>
                </div>
            </div>

            <!-- Minimal Button -->
            <button 
                @click="router.visit(route('tasks.index'))"
                class="mt-auto group/btn flex items-center gap-4 py-6 border-t border-white/[0.05] text-[#8c7e6a] hover:text-[#d9c5a3] transition-all"
            >
                <div class="h-10 w-10 rounded-xl border border-white/[0.1] flex items-center justify-center group-hover/btn:border-[#d9c5a3]/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </div>
                <div class="text-left">
                    <span class="text-[9px] uppercase tracking-[0.3em] font-black">Open Ledger</span>
                    <p class="text-[7px] uppercase tracking-[0.1em] text-[#3d352e] mt-0.5">View all history</p>
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
