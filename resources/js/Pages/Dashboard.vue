<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import JournalEditor from '@/Components/Journal/JournalEditor.vue';
import TaskWidget from '@/Components/Tasks/TaskWidget.vue';

const tasks = ref([]);

const formattedDate = new Intl.DateTimeFormat('en-US', {
    weekday: 'long',
    month: 'short',
    day: '2-digit'
}).format(new Date());

defineProps({
    entries: Array,
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
        onSuccess: () => 
        form.reset(),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex flex-col lg:flex-row h-full bg-[#161412] p-4 md:p-6 lg:p-8 gap-6 lg:gap-10 overflow-x-hidden min-h-[calc(100vh-64px)] font-serif">
            
            <JournalEditor 
                :form="form" 
                :formattedDate="formattedDate"
                @submit="submitEntry"
            />

            <TaskWidget 
                :tasks="tasks"
            />

        </div>
    </AuthenticatedLayout>
</template>
