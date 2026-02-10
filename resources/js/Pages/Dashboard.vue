<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import JournalEditor from '@/Components/Journal/JournalEditor.vue';
import TaskWidget from '@/Components/Tasks/TaskWidget.vue';

const formattedDate = new Intl.DateTimeFormat('en-US', {
    weekday: 'long',
    month: 'short',
    day: '2-digit'
}).format(new Date());

const props = defineProps({
    entryCount: Number,
    tasks: Array,
})

const form = useForm({
    title: '',
    content: '',
    entry_date: new Date().toISOString().split('T')[0],
    image: null,
    video: null,
    audio: null,
    file: null,
})

const submitEntry = () => {
    form.post(route('journal.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Home - Scriptorium" />

    <AuthenticatedLayout>
        <div class="h-screen bg-[#11100f] flex flex-col overflow-hidden relative font-serif">
            <!-- Subtle Atmospheric Glow -->
            <div class="absolute top-1/4 left-1/4 w-[40rem] h-[40rem] bg-[#8b2635]/5 blur-[120px] rounded-full pointer-events-none"></div>
            
            <!-- Minimal Texture -->
            <div class="absolute inset-0 opacity-[0.015] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>

            <main class="flex-1 flex flex-col relative z-20 overflow-hidden px-8 md:px-12 lg:px-20 py-10 lg:py-16">
                <div class="max-w-7xl mx-auto w-full flex-1 flex flex-col min-h-0">
                    
                    <!-- Clean Header -->
                    <header class="mb-12 lg:mb-20 flex flex-col lg:flex-row items-end justify-between gap-6 min-h-[80px]">
                        <div>
                            <h1 class="text-4xl lg:text-5xl font-bold font-cinzel text-[#f4e4bc] tracking-tight">
                                The <span class="text-[#8b2635]">Chronicler's</span> Desk
                            </h1>
                            <p class="mt-3 text-[#8c7e6a] text-lg opacity-60 italic">{{ formattedDate }}</p>
                        </div>

                        <div class="hidden lg:flex items-center gap-10 pb-2 border-b border-white/[0.05]">
                            <div class="text-right">
                                <p class="text-[9px] uppercase tracking-[0.4em] text-[#8c7e6a] font-black">Memory Bloom</p>
                                <p class="text-xl font-cinzel text-[#d9c5a3]">{{ entryCount || 0 }} Entries</p>
                            </div>
                        </div>
                    </header>

                    <!-- Balanced Content Row -->
                    <div class="flex-1 flex flex-col lg:flex-row gap-16 lg:gap-24 min-h-0">
                        
                        <!-- Minimal Sidebar (Tasks) -->
                        <aside class="hidden xl:flex flex-col w-[18rem] shrink-0 min-h-0">
                            <TaskWidget :tasks="tasks" />
                        </aside>

                        <!-- Focused Writing Area (Editor) -->
                        <div class="flex-1 flex flex-col min-h-0">
                            <JournalEditor 
                                :form="form" 
                                :formattedDate="formattedDate"
                                @submit="submitEntry"
                            />
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.font-serif {
    font-family: 'Cormorant Garamond', serif;
}
</style>
