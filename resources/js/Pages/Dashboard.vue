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
        <div class="min-h-screen flex flex-col relative font-serif">
            <main class="flex-1 flex flex-col relative z-20 px-4 md:px-12 lg:px-20 py-8 lg:py-16 overflow-y-auto">
                <div class="max-w-7xl mx-auto w-full flex-1 flex flex-col">
                    
                    <!-- Clean Header -->
                    <header class="mb-12 lg:mb-20 flex flex-col lg:flex-row items-end justify-between gap-6 min-h-[80px]">
                        <div>
                            <h1 class="text-3xl lg:text-5xl font-bold font-cinzel text-[#C9B79C] tracking-tight">
                                The <span class="text-[#8C6A4A]">Chronicler's</span> Desk
                            </h1>
                            <p class="mt-3 text-[#C9B79C]/40 text-lg italic">{{ formattedDate }}</p>
                        </div>

                        <div class="hidden lg:flex items-center gap-10 pb-2 border-b border-white/[0.03]">
                            <div class="text-right">
                                <p class="text-[9px] uppercase tracking-[0.4em] text-[#8C6A4A] font-black">Memory Bloom</p>
                                <p class="text-xl font-cinzel text-[#C9B79C]">{{ entryCount || 0 }} Entries</p>
                            </div>
                        </div>
                    </header>

                    <!-- Balanced Content Row -->
                    <div class="flex-1 flex flex-col lg:flex-row gap-8 lg:gap-16 relative">
                        <!-- Sidebar (Tasks) -->
                        <aside class="flex flex-col w-full lg:w-[18rem] shrink-0 order-2 lg:order-1 relative z-20">
                            <TaskWidget :tasks="tasks" />
                        </aside>

                        <!-- Subtle Vertical Divider -->
                        <div class="hidden lg:block absolute left-[18.2rem] top-0 bottom-0 w-[1px] bg-gradient-to-b from-[#8C6A4A]/20 via-[#8C6A4A]/5 to-transparent pointer-events-none"></div>

                        <!-- Focused Writing Area (Editor) -->
                        <div class="flex-1 flex flex-col order-1 lg:order-2 pl-0 lg:pl-12">
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
