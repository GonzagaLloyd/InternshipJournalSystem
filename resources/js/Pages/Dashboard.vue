<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const journalEntry = ref("");
const tasks = ref([]);
</script>

<template>
    <Head title="Journal" />

    <AuthenticatedLayout>
        <div class="flex h-full bg-[#161412] p-8 gap-8 overflow-hidden min-h-[calc(100vh-64px)]">
            <!-- Main Ledger -->
            <div class="flex-1 flex flex-col bg-[#25211e] border border-[#3d352e] rounded-3xl shadow-2xl overflow-hidden relative">
                <!-- Vellum Texture Overlay -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/parchment.png')]"></div>
                
                <div class="p-8 border-b border-[#3d352e] bg-black/10 flex justify-between items-center relative z-10">
                    <div>
                        <h2 class="text-3xl font-serif font-black text-[#d9c5a3] italic tracking-tight">Monday, Feb 9</h2>
                        <p class="text-[10px] uppercase tracking-[0.3em] text-[#8c7e6a] mt-1 font-bold">OJT Daily Ledger</p>
                    </div>
                    <button class="bg-[#8b2635] text-[#f4e4bc] px-10 py-4 rounded-2xl font-serif font-black uppercase tracking-widest text-[10px] shadow-lg hover:bg-[#bc4749] transition-all active:scale-95 border border-[#8b2635]/20">
                        Seal Record
                    </button>
                </div>
                
                <textarea 
                    v-model="journalEntry"
                    placeholder="Inscribe today's progress..."
                    class="flex-1 bg-transparent border-none focus:ring-0 p-12 text-xl font-serif font-medium leading-loose text-[#d9c5a3] placeholder:text-[#3d352e] resize-none relative z-10 scrollbar-hide"
                ></textarea>
            </div>

            <!-- Side Scriptorium -->
            <div class="w-80 flex flex-col gap-6 shrink-0 h-full">
                <div class="flex-1 bg-[#25211e] border border-[#3d352e] rounded-3xl p-8 flex flex-col shadow-2xl relative overflow-hidden">
                    <div class="absolute inset-0 opacity-[0.02] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/parchment.png')]"></div>
                    
                    <h3 class="text-[10px] uppercase tracking-[0.3em] text-[#8c7e6a] mb-8 font-black relative z-10">Tasks of the Day</h3>
                    
                    <div class="flex-1 overflow-y-auto pr-2 scrollbar-hide space-y-5 relative z-10">
                        <div 
                            v-for="task in tasks" 
                            :key="task.id" 
                            class="flex items-start gap-4 group cursor-pointer" 
                            @click="task.completed = !task.completed"
                        >
                            <div 
                                :class="task.completed ? 'bg-[#8b2635] border-[#8b2635]' : 'border-[#3d352e] bg-black/20'"
                                class="h-5 w-5 rounded-md border-2 mt-0.5 shrink-0 flex items-center justify-center transition-all group-hover:border-[#8b2635]/50 shadow-sm"
                            >
                                <svg v-if="task.completed" class="h-3 w-3 text-[#f4e4bc]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span 
                                :class="task.completed ? 'line-through text-[#5c4d44]' : 'text-[#d9c5a3]'" 
                                class="text-sm font-serif font-medium leading-snug select-none transition-colors group-hover:text-[#f4e4bc]"
                            >
                                {{ task.name }}
                            </span>
                        </div>
                    </div>
                    
                    <button class="mt-8 pt-6 border-t border-[#3d352e] text-[10px] uppercase tracking-widest text-[#8c7e6a] hover:text-[#d9c5a3] transition-colors font-black flex items-center gap-2 relative z-10">
                        <span class="text-lg leading-none">+</span> New Task
                    </button>
                </div>
            </div>
        </div>
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
</style>

