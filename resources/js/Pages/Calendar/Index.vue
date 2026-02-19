<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineAsyncComponent } from 'vue';
import { useTabSync } from '@/Composables/useTabSync';

const ChronosDial = defineAsyncComponent(() => import('@/Components/Calendar/ChronosDial.vue'));

useTabSync(['events']);

const props = defineProps({
    events: {
        type: Object,
        default: null
    }
});
</script>

<template>
    <Head title="Celestial Horologium" />

    <AuthenticatedLayout 
        title="Celestial <span class='text-brass'>Horologium</span>"
        subtitle="The Tapestry of Time"
    >
        <div class="xl:h-[calc(100vh-5rem)] xl:overflow-hidden flex flex-col relative font-serif text-cream">
            <main class="flex-1 flex flex-col relative z-20 px-4 md:px-8 xl:px-12 py-6 xl:py-8 xl:min-h-0">
                <div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col xl:min-h-0">
                    
                    <!-- Atmospheric Background (Consistent across pages) -->
                    <div class="absolute inset-0 pointer-events-none z-0">
                        <div class="absolute top-[10%] left-[20%] w-[50rem] h-[50rem] bg-[#8C6A4A]/5 blur-[120px] rounded-full"></div>
                        <div class="absolute bottom-[5%] right-[10%] w-[30rem] h-[30rem] bg-[#C9B79C]/3 blur-[100px] rounded-full"></div>
                    </div>

                    <div class="flex-1 flex flex-col min-h-0 relative z-10 transition-all duration-500">
                        <ChronosDial :events="events" />
                    </div>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>
