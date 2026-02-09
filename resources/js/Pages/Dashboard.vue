<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const mood = ref(null);
const moods = [
    { label: 'Inspired', color: 'text-[#d9c5a3]' },
    { label: 'Steady', color: 'text-[#8c7e6a]' },
    { label: 'Solemn', color: 'text-[#5c4d44]' },
];

const journalEntry = ref("");
const tasks = ref([
    { id: 1, name: 'Setup Laravel Environment', completed: true, priority: 'URGENT', color: 'bg-[#8b2635]/10 text-[#bc4749] border-[#8b2635]/20' },
    { id: 2, name: 'Integrate Airtable for DB', completed: false, priority: 'SCRIBE', color: 'bg-[#b8860b]/10 text-[#d9c5a3] border-[#b8860b]/20' },
    { id: 3, name: 'Design Sidebar UI in Vue', completed: false, priority: 'ACTIVE', color: 'bg-[#d9c5a3]/5 text-[#d9c5a3] border-[#d9c5a3]/10' },
    { id: 4, name: 'Document daily progress', completed: false, priority: 'ARCHIVE', color: 'bg-black/20 text-[#8c7e6a] border-white/5' },
]);

const attachments = ref([
    { name: 'scroll_v2.png', size: '3.1 MB', type: 'image' },
    { name: 'manuscript_draft.pdf', size: '840 KB', type: 'file' },
]);
</script>

<template>
    <Head title="Journal" />

    <AuthenticatedLayout>
        <div class="flex h-full min-h-[calc(100vh-64px)] overflow-hidden p-6 gap-6 bg-[#161412]">
            <!-- Middle Column: Daily Log -->
            <div class="flex-1 glass-card flex flex-col min-w-0 bg-[#25211e] border-[#3d352e] rounded-2xl overflow-hidden">
                <!-- Header -->
                <div class="p-8 border-b border-[#3d352e] shrink-0 bg-black/10">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black text-[#8b2635] uppercase tracking-[0.2em] mb-2 font-serif">Entry Record</p>
                            <h2 class="text-3xl font-serif font-black text-[#d9c5a3] tracking-tight">Monday, Feb 9</h2>
                        </div>
                        
                        <div class="flex items-center space-x-6">
                            <span class="text-[10px] font-black text-[#8c7e6a] uppercase tracking-widest italic font-serif">State of Mind</span>
                            <div class="flex space-x-2 bg-black/20 p-1 rounded-xl border border-[#3d352e]">
                                <button 
                                    v-for="m in moods" 
                                    :key="m.label"
                                    @click="mood = m.label"
                                    :class="[
                                        mood === m.label ? 'bg-[#8b2635] text-[#f4e4bc] shadow-xl shadow-black/40 scale-105' : 'hover:bg-white/[0.03] text-[#8c7e6a]',
                                        'px-4 py-2 rounded-lg flex items-center justify-center text-[10px] font-black uppercase tracking-widest transition-all duration-300 font-serif'
                                    ]"
                                >
                                    {{ m.label }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editor -->
                <div class="flex-1 overflow-y-auto p-8 space-y-10 scrollbar-hide bg-transparent">
                    <div class="relative group">
                        <textarea 
                            v-model="journalEntry"
                            placeholder="Inscribe the day's achievements..."
                            class="w-full bg-transparent border-none focus:ring-0 text-[#d9c5a3] placeholder:text-[#5c4d44] text-xl font-serif font-medium min-h-[400px] resize-none leading-relaxed selection:bg-[#8b2635]/30"
                        ></textarea>
                    </div>

                    <!-- Work Assets -->
                    <div class="space-y-6 pt-12 border-t border-[#3d352e]">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-black text-[#8c7e6a] uppercase tracking-[0.2em] font-serif">Inscribed Assets</h4>
                            <button class="text-[10px] font-black text-[#8b2635] uppercase tracking-widest hover:text-[#bc4749] transition-colors font-serif">Clear Scroll</button>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <button class="border-2 border-dashed border-[#3d352e] rounded-2xl p-6 flex flex-col items-center justify-center hover:border-[#8b2635]/50 hover:bg-white/[0.02] transition-all group">
                                <div class="h-10 w-10 bg-[#8b2635]/10 rounded-xl flex items-center justify-center text-[#8b2635] group-hover:scale-110 transition-transform mb-3">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                                <span class="text-[10px] text-[#8c7e6a] font-black uppercase tracking-widest font-serif">Bind File</span>
                            </button>

                            <div v-for="file in attachments" :key="file.name" class="bg-black/10 border border-[#3d352e] rounded-2xl p-4 flex items-center group hover:bg-black/20 transition-all cursor-pointer">
                                <div class="h-10 w-10 rounded-lg bg-[#1a1715] flex items-center justify-center text-[8px] font-black text-[#8c7e6a] group-hover:bg-[#8b2635] group-hover:text-[#f4e4bc] transition-all">
                                    {{ file.name.split('.').pop().toUpperCase() }}
                                </div>
                                <div class="ml-4 min-w-0">
                                    <p class="text-[11px] font-black text-[#d9c5a3] truncate tracking-tight font-serif">{{ file.name }}</p>
                                    <p class="text-[9px] font-bold text-[#8c7e6a] uppercase">{{ file.size }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="p-6 bg-[#1c1a18] border-t border-[#3d352e]">
                    <button class="w-full bg-black/10 hover:bg-black/20 text-[#8c7e6a] hover:text-[#d9c5a3] py-4 rounded-xl text-xs font-black uppercase tracking-[0.2em] border border-[#3d352e] transition-all flex items-center justify-center space-x-3">
                        <svg class="h-4 w-4 text-[#8b2635]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span class="font-serif">Commit shift to historical record</span>
                    </button>
                </div>
            </div>

            <!-- Right Column: Tasks Board -->
            <div class="w-[420px] flex flex-col shrink-0 gap-6">
                <div class="bg-[#25211e] border border-[#3d352e] rounded-2xl flex-1 flex flex-col overflow-hidden shadow-2xl">
                    <div class="p-8 border-b border-[#3d352e] bg-black/10 flex justify-between items-center">
                        <h3 class="text-xl font-serif font-black text-[#d9c5a3] italic tracking-tight uppercase">Scriptorium</h3>
                        <button class="bg-[#8b2635] text-[#f4e4bc] h-10 w-10 rounded-xl shadow-lg shadow-black/40 hover:scale-105 active:scale-95 transition-all text-2xl font-black flex items-center justify-center">+</button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 space-y-3 scrollbar-hide bg-transparent">
                        <div 
                            v-for="task in tasks" 
                            :key="task.id"
                            class="group flex items-center bg-black/10 border border-[#3d352e] p-5 rounded-2xl transition-all hover:bg-black/20 hover:scale-[1.01]"
                        >
                            <button 
                                @click="task.completed = !task.completed"
                                :class="[
                                    task.completed ? 'bg-[#8b2635] border-[#8b2635]' : 'bg-transparent border-[#3d352e]',
                                    'h-5 w-5 rounded-md border-2 flex items-center justify-center transition-all'
                                ]"
                            >
                                <svg v-if="task.completed" class="h-3 w-3 text-[#f4e4bc]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            
                            <div class="mx-5 flex-1 min-w-0">
                                <p :class="[task.completed ? 'line-through text-[#5c4d44]' : 'text-[#d9c5a3]', 'text-[13px] font-bold tracking-tight truncate transition-all font-serif']">
                                    {{ task.name }}
                                </p>
                            </div>

                            <span :class="['text-[8px] font-black px-2 py-1 rounded-lg uppercase tracking-widest border', task.color]">
                                {{ task.priority }}
                            </span>
                        </div>
                    </div>

                    <!-- Report Export -->
                    <div class="p-8 bg-[#1c1a18] border-t border-[#3d352e]">
                        <button class="w-full bg-[#8b2635] hover:bg-[#bc4749] text-[#f4e4bc] py-5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-xl shadow-black/40 active:scale-95 flex items-center justify-center group overflow-hidden relative">
                            <span class="relative z-10 font-serif">Seal Weekly OJT Ledger</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>


<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>


<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

