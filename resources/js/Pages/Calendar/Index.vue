<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChronosDial from '@/Components/Calendar/ChronosDial.vue';

const props = defineProps({
    events: Object // Grouped by date 'Y-m-d'
});
</script>

<template>
    <Head title="Celestial Horologium" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 lg:left-72 flex flex-col">
            <div class="p-4 md:p-8 lg:p-12 h-full flex flex-col font-serif relative overflow-hidden">
                <!-- Atmospheric Background -->
                <div class="fixed inset-0 pointer-events-none z-0">
                    <div class="absolute top-[15%] left-[20%] w-[60rem] h-[60rem] bg-[#8C6A4A]/5 blur-[150px] rounded-full"></div>
                    <div class="absolute bottom-[10%] right-[10%] w-[40rem] h-[40rem] bg-[#C9B79C]/3 blur-[120px] rounded-full"></div>
                </div>

                <ChronosDial :events="events" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
