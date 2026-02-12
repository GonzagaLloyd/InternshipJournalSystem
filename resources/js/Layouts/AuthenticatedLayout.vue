<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Navigation/Sidebar.vue';
import Topbar from '@/Components/Navigation/Topbar.vue';
import { Link } from '@inertiajs/vue3';
import Toast from '@/Components/UI/Toast.vue';

defineProps({
    title: String,
    subtitle: String,
});

const showingMobileMenu = ref(false);
const isCollapsed = ref(false);

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
    <div class="min-h-screen flex font-serif text-[#E3D5C1] select-none bg-[#1B1B1B]">
        <!-- Sidebar Navigation -->
        <Sidebar 
            :showingMobileMenu="showingMobileMenu" 
            :isCollapsed="isCollapsed"
            @close="showingMobileMenu = false"
        />

            <!-- Main Content Area -->
            <div 
                :class="[
                    isCollapsed ? 'lg:pl-24' : 'lg:pl-72',
                    'flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out relative z-10'
                ]"
            >
                <!-- Centralized Atmosphere (Matches Dashboard) -->
                <div class="fixed inset-0 pointer-events-none z-0">
                    <!-- Subtle Atmospheric Glow -->
                    <div class="absolute top-1/4 left-1/4 w-[40rem] h-[40rem] bg-[#8C6A4A]/5 blur-[120px] rounded-full"></div>
                    <!-- Minimal Texture -->
                    <div class="absolute inset-0 opacity-[0.2] bg-[url('https://www.transparenttextures.com/patterns/dust.png')]"></div>
                </div>

                <!-- Modular Topbar -->
                <Topbar 
                    :title="title" 
                    :subtitle="subtitle"
                    :isCollapsed="isCollapsed"
                    @toggle-sidebar="toggleSidebar"
                    @open-mobile-menu="showingMobileMenu = true"
                />

                <!-- Main Content -->
                <main class="flex-1 min-h-0 relative z-10">
                    <slot />
                </main>

                <Toast />
            </div>
    </div>
</template>




