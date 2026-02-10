<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';
import TaskItem from '@/Components/Tasks/TaskItem.vue';
import TaskCreateModal from '@/Components/Tasks/TaskCreateModal.vue';

const props = defineProps({
    tasks: Array
});

const isNavigating = ref(false);
const showCreateModal = ref(false);

const toggleTask = (task) => {
    console.log('Task object:', task);
    console.log('Task ID:', task.id);
    
    if (!task.id) {
        console.error('Task ID is missing!', task);
        return;
    }
    
    router.patch(route('tasks.toggle', { task: task.id }), {}, {
        preserveScroll: true
    });
};

const deleteTask = (task) => {
    if (confirm('Are you sure you want to strike this task from the records?')) {
        if (!task.id) {
            console.error('Task ID is missing!', task);
            return;
        }
        
        router.delete(route('tasks.destroy', { task: task.id }), {
            preserveScroll: true
        });
    }
};

const sortedTasks = computed(() => {
    return [...props.tasks].sort((a, b) => {
        if (a.completed !== b.completed) {
            return a.completed ? 1 : -1;
        }
        return new Date(b.created_at) - new Date(a.created_at);
    });
});
</script>

<template>
    <Head title="Scriptorium - Tasks" />

    <AuthenticatedLayout>
        <div class="h-full bg-[#161412] p-4 md:p-6 lg:p-8 flex flex-col font-cormorant min-h-[calc(100vh-64px)] overflow-hidden relative">
            <!-- Dust Specs Layer -->
            <div class="absolute inset-0 pointer-events-none z-20 overflow-hidden">
                <div class="dust-particles opacity-[0.1]"></div>
            </div>

            <!-- Background Texture Overlay -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>
            
            <div class="max-w-6xl mx-auto w-full flex-1 flex flex-col relative z-20">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-12 lg:mb-16 relative z-10">
                    <div class="relative group w-full md:w-auto text-center md:text-left">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-cinzel tracking-tight mb-3 transition-transform duration-700 group-hover:scale-[1.01]">
                            <span class="bg-gradient-to-b from-[#f4e4bc] via-[#d9c5a3] to-[#a8967a] bg-clip-text text-transparent drop-shadow-md whitespace-nowrap">
                                Active Decrees
                            </span>
                        </h1>
                        <div class="flex items-center gap-4 justify-center md:justify-start">
                            <div class="h-[1px] flex-1 md:flex-none md:w-16 bg-gradient-to-r from-transparent via-[#8b2635]/60 to-transparent"></div>
                            <p class="text-[9px] sm:text-[10px] uppercase tracking-[0.4em] sm:tracking-[0.6em] text-[#8b2635] font-black font-sans opacity-95">The Scriptorium's Mandate</p>
                            <div class="h-[1px] flex-1 md:flex-none md:w-16 bg-gradient-to-r from-transparent via-[#8b2635]/60 to-transparent"></div>
                        </div>
                    </div>

                    <button 
                        @click="showCreateModal = true"
                        class="w-full md:w-auto group flex items-center justify-center gap-4 px-10 py-4 bg-[#8b2635]/10 border border-[#8b2635]/30 rounded-2xl text-[#f4e4bc] hover:bg-[#8b2635] transition-all duration-500 shadow-lg hover:shadow-[#8b2635]/20"
                    >
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] font-sans">Append Decree</span>
                        <div class="h-6 w-6 rounded-full border border-[#f4e4bc]/30 flex items-center justify-center group-hover:border-[#f4e4bc] transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12m6-6H6" />
                            </svg>
                        </div>
                    </button>
                </div>

                <!-- Task List -->
                <div class="flex-1 overflow-y-auto scrollbar-hide pr-2">
                    <div class="divide-y divide-[#3d352e]/10">
                        <TaskItem 
                            v-for="task in sortedTasks" 
                            :key="task.id"
                            :task="task"
                            @toggle="toggleTask"
                            @delete="deleteTask"
                        />
                    </div>

                    <!-- Empty State -->
                    <div v-if="tasks.length === 0" class="flex flex-col items-center justify-center py-32 animate-pulse-slow">
                        <div class="relative mb-10">
                            <svg class="w-32 h-32 text-[#3d352e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <div class="absolute inset-0 bg-radial-gradient from-[#8b2635]/10 to-transparent blur-2xl"></div>
                        </div>
                        <h3 class="text-3xl font-cinzel text-[#8c7e6a] uppercase tracking-[0.5em] opacity-40">The Ledger is Satisfied</h3>
                        <p class="text-[#3d352e] text-[10px] uppercase tracking-[0.4em] mt-4 font-sans font-black">No decrees remain outstanding</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal Component -->
        <TaskCreateModal :show="showCreateModal" @close="showCreateModal = false" />

        <TomeLoader :show="isNavigating" message="Consulting the Scriptorium..." />
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

.dust-particles {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: 
        radial-gradient(1px 1px at 20px 30px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 40px 70px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 50px 160px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 90px 40px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 130px 80px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 160px 120px, #f4e4bc, rgba(0,0,0,0));
    background-repeat: repeat;
    background-size: 200px 200px;
    animation: dust 60s linear infinite;
}

@keyframes dust {
    from { transform: translateY(0); }
    to { transform: translateY(-200px); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.8; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.02); }
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}
</style>
