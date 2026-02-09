<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Navigation/Sidebar.vue';
import Dropdown from '@/Components/Navigation/Dropdown.vue';
import DropdownLink from '@/Components/Navigation/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const showingMobileMenu = ref(false);
const isCollapsed = ref(false);

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
    <div class="min-h-screen flex font-inter text-gray-900 overflow-hidden select-none">
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
            <!-- Minimal Topbar (Transparent) -->
            <header class="h-16 flex items-center justify-between px-8 z-30 shrink-0">
                <div class="flex items-center">
                    <button 
                        @click="showingMobileMenu = true" 
                        type="button" 
                        class="lg:hidden p-2 rounded-xl text-white/70 hover:text-white hover:bg-white/10 transition-all shrink-0"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <button 
                        @click="toggleSidebar"
                        type="button"
                        class="hidden lg:flex p-2 rounded-xl text-white/40 hover:text-white hover:bg-white/10 transition-all focus:outline-none shrink-0"
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
                    <!-- Search Button -->
                    <button class="text-white/40 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    <!-- Settings -->
                    <button class="text-white/40 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    </button>
                    <!-- Bell -->
                    <button class="relative text-white/40 hover:text-white transition-colors">
                        <span class="absolute -top-1 -right-1 h-3 w-3 bg-rose-500 rounded-full border-2 border-[#1a1a1a]"></span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-hidden">
                <slot />
            </main>
        </div>
    </div>
</template>




