<script setup>
defineProps({
    tasks: Array
});
</script>

<template>
    <div class="w-full lg:w-[22rem] flex flex-col shrink-0 order-2 lg:order-none">
        <div class="flex-1 bg-[#25211e] border border-[#3d352e] rounded-[1.5rem] lg:rounded-[2rem] p-6 lg:p-10 flex flex-col shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 opacity-[0.02] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/parchment.png')]"></div>
            
            <div class="mb-8 lg:mb-10 relative z-10">
                <h3 class="text-[10px] uppercase tracking-[0.4em] text-[#8c7e6a] font-black flex items-center gap-3">
                    Scriptorium
                    <span class="h-1 w-1 rounded-full bg-[#8b2635]"></span>
                </h3>
            </div>

            <div class="flex-1 overflow-y-auto pr-2 lg:pr-4 scrollbar-hide space-y-6 lg:space-y-8 relative z-10">
                <div 
                    v-for="task in tasks" 
                    :key="task.id" 
                    class="group/item flex items-start gap-4 lg:gap-5 cursor-pointer" 
                    @click="task.completed = !task.completed"
                >
                    <div 
                        :class="task.completed ? 'bg-[#8b2635] border-[#8b2635]' : 'border-[#3d352e] bg-black/40'"
                        class="h-5 w-5 rounded-lg border-2 mt-1 shrink-0 flex items-center justify-center transition-all group-hover/item:border-[#8b2635] shadow-lg shadow-black/20"
                    >
                        <svg v-if="task.completed" class="h-3 w-3 text-[#f4e4bc]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span 
                        :class="task.completed ? 'line-through text-[#5c4d44]' : 'text-[#d9c5a3] group-hover/item:text-white'" 
                        class="text-[14px] lg:text-[15px] font-medium leading-relaxed transition-all"
                    >
                        {{ task.name }}
                    </span>
                </div>
            </div>

            <!-- Simple Add Task -->
            <button class="mt-8 lg:mt-10 flex items-center gap-4 text-[#8c7e6a] hover:text-[#f4e4bc] transition-all group/add relative z-10 pt-6 lg:pt-8 border-t border-[#3d352e]">
                <div class="h-8 w-8 rounded-full border border-dashed border-[#8c7e6a] flex items-center justify-center text-xl group-hover/add:border-[#8b2635] group-hover/add:text-[#8b2635] transition-all">
                    +
                </div>
                <span class="text-[10px] uppercase tracking-[0.2em] font-black">Append Task</span>
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
