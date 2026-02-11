<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Navigation/Sidebar.vue';
import Dropdown from '@/Components/Navigation/Dropdown.vue';
import DropdownLink from '@/Components/Navigation/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import Toast from '@/Components/UI/Toast.vue';

const showingMobileMenu = ref(false);
const isCollapsed = ref(false);

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
    <div class="min-h-screen flex font-serif text-[#C9B79C] select-none bg-[#1B1B1B]">
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

                <!-- Minimal Topbar (Transparent) -->
                <header class="h-16 flex items-center justify-between px-4 lg:px-8 z-30 shrink-0">
                    <div class="flex items-center">
                        <button 
                            @click="showingMobileMenu = true" 
                            type="button" 
                            class="lg:hidden p-2 rounded-xl text-[#8C6A4A]/60 hover:text-[#C9B79C] transition-all shrink-0"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <button 
                            @click="toggleSidebar"
                            type="button"
                            class="hidden lg:flex p-2 rounded-xl text-[#8C6A4A]/40 hover:text-[#C9B79C] transition-all focus:outline-none shrink-0"
                        >
                            <svg 
                                :class="['h-6 w-6 transition-transform duration-500', isCollapsed ? 'rotate-180' : '']" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h10M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Bell -->
                        <button class="relative text-[#8C6A4A]/40 hover:text-[#C9B79C] transition-colors">
                            <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 bg-[#525947] rounded-full border border-[#1B1B1B]"></span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </button>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex-1 min-h-0 relative z-10">
                    <slot />
                </main>

                <Toast />
            </div>
    </div>
</template>




