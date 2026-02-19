<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';
import { useTabSync } from '@/Composables/useTabSync';

// Async components for performance optimization
const JournalEditor = defineAsyncComponent(() => import('@/Components/Journal/JournalEditor.vue'));
const TaskWidget = defineAsyncComponent(() => import('@/Components/Tasks/TaskWidget.vue'));
const ActivityHeatmap = defineAsyncComponent(() => import('@/Components/UI/ActivityHeatmap.vue'));

useTabSync([]); // Reload all props

const formattedDate = new Intl.DateTimeFormat('en-US', {
    weekday: 'long',
    month: 'short',
    day: '2-digit'
}).format(new Date());

const props = defineProps({
    entryCount: Number,
    tasks: { type: Array, default: null },
    activity: { type: Object, default: null },
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

    <AuthenticatedLayout 
        title="The <span class='text-brass'>Chronicler's</span> Desk"
        :subtitle="formattedDate"
    >
        <!-- Responsive Layout Logic: Locked on Desktop, Scrollable on Mobile -->
        <div class="xl:h-[calc(100vh-5rem)] xl:overflow-hidden flex flex-col relative font-serif text-cream">
            <main class="flex-1 flex flex-col relative z-20 px-4 md:px-8 xl:px-12 py-6 xl:py-8 xl:min-h-0">
                <div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col xl:min-h-0">
                    
                    <!-- Dynamic Layout: Vertical stack on mobile, horizontal locked on desktop -->
                    <div class="flex-1 flex flex-col xl:flex-row gap-8 xl:gap-20 relative xl:min-h-0">
                        <!-- Focused Writing Area (Editor) - Top on Mobile, Right on Desktop -->
                        <div class="flex-1 flex flex-col order-1 xl:order-2 xl:pl-16 min-w-0 xl:min-h-0 min-h-[600px] xl:min-h-0">
                            <JournalEditor 
                                :form="form" 
                                :formattedDate="formattedDate"
                                @submit="submitEntry"
                            />
                        </div>

                        <!-- Sidebar (Tasks) - Bottom on Mobile, Left on Desktop -->
                        <aside class="flex flex-col w-full xl:w-[24rem] lg:max-w-xl lg:mx-auto xl:mx-0 shrink-0 order-2 xl:order-1 relative z-20 xl:min-h-0 pb-12 xl:pb-0 space-y-10">
                            <!-- Continuity Heatmap -->
                            <ActivityHeatmap :activity="activity" />

                            <TaskWidget :tasks="tasks" />
                        </aside>

                        <!-- Subtle Vertical Divider (Desktop Only) -->
                        <div class="hidden xl:block absolute left-[24.2rem] top-0 bottom-0 w-[1px] bg-gradient-to-b from-brass/30 via-brass/10 to-transparent pointer-events-none"></div>
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
