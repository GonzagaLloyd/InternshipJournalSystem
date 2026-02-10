<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    showingMobileMenu: Boolean,
    isCollapsed: Boolean,
});

const user = usePage().props.auth.user;
const emit = defineEmits(['close']);

const menuItems = [
    { name: 'Home', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', routeName: 'dashboard' },
    { name: 'Entries', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', routeName: 'journal.index' },
    { name: 'Tasks', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', routeName: '#' },
    { name: 'Calendar', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', routeName: '#' },
];

const managementItems = [
    { name: 'Reports', icon: 'M9 17v-2a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm3 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM9 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm3 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2z' },
    { name: 'Vault', icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' },
];
</script>

<template>
    <!-- Desktop Sidebar -->
    <aside 
        :class="[
            isCollapsed ? 'w-24' : 'w-72',
            'hidden lg:flex flex-col border-r border-[#3d352e] bg-[#1c1a18] fixed h-screen transition-all duration-300 ease-in-out z-40 text-[#d9c5a3] shadow-2xl overflow-hidden'
        ]"
    >
        <!-- Logo Section -->
        <div :class="['h-28 flex items-center shrink-0 transition-all', isCollapsed ? 'justify-center w-full' : 'px-8']">
            <Link :href="route('dashboard')" :class="['flex items-center group', isCollapsed ? 'justify-center' : 'space-x-4']">
                <div class="flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                    <ApplicationLogo :class="[isCollapsed ? 'h-11' : 'h-16', 'w-auto opacity-90 group-hover:opacity-100 transition-all']" />
                </div>
                <span v-if="!isCollapsed" class="text-2xl font-serif font-black tracking-widest text-[#d9c5a3] uppercase italic">OJT</span>
            </Link>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-4 scrollbar-hide">
            <div class="px-4 space-y-2">
                <Link 
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.routeName === '#' ? '#' : route(item.routeName)"
                    :class="[
                        route().current(item.routeName) ? 'bg-[#3d352e] text-white shadow-xl' : 'text-[#8c7e6a] hover:bg-[#2d2a27] hover:text-[#d9c5a3]',
                        isCollapsed ? 'h-12 w-12 mx-auto justify-center rounded-xl p-0' : 'px-5 py-4 rounded-[1.5rem]',
                        'flex items-center transition-all duration-300 group relative'
                    ]"
                >
                    <div :class="[isCollapsed ? '' : 'mr-5']">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" />
                        </svg>
                    </div>
                    <span v-if="!isCollapsed" class="text-sm font-bold truncate font-serif">{{ item.name }}</span>
                </Link>
            </div>

            <div class="my-6 border-t border-[#3d352e] mx-8" v-show="!isCollapsed"></div>

            <div class="px-4 space-y-2">
                <Link 
                    v-for="sub in managementItems"
                    :key="sub.name"
                    href="#"
                    :class="[
                        isCollapsed ? 'h-12 w-12 mx-auto justify-center rounded-xl p-0' : 'px-5 py-4 rounded-[1.5rem]',
                        'flex items-center text-[#8c7e6a] hover:bg-[#2d2a27] hover:text-[#d9c5a3] transition-all duration-300'
                    ]"
                >
                    <div :class="[isCollapsed ? '' : 'mr-5']">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="sub.icon" />
                        </svg>
                    </div>
                    <span v-if="!isCollapsed" class="text-sm font-bold truncate font-serif">{{ sub.name }}</span>
                </Link>
            </div>
        </div>

        <!-- Footer / User Profile -->
        <div class="p-6 mt-auto border-t border-[#3d352e] bg-black/10">
            <div :class="['flex items-center transition-all hover:bg-[#2d2a27] cursor-pointer group', isCollapsed ? 'justify-center h-10 w-10 mx-auto rounded-lg' : 'rounded-2xl border border-[#3d352e] p-3']">
                <div :class="[isCollapsed ? 'h-8 w-8' : 'h-10 w-10', 'bg-[#8b2635] rounded-lg flex-shrink-0 flex items-center justify-center text-[#f4e4bc] font-black text-xs shadow-lg shadow-black/40 transform transition-transform group-hover:scale-105']">
                    {{ user.name.charAt(0) }}
                </div>
                <div v-if="!isCollapsed" class="ml-4 flex-1 min-w-0">
                    <p class="text-[13px] font-black text-white truncate tracking-tight font-serif">{{ user.name }}</p>
                    <p class="text-[10px] font-bold text-[#8c7e6a] truncate group-hover:text-[#d9c5a3] transition-colors uppercase tracking-widest">Scribe Active</p>
                </div>
                <Link 
                    v-if="!isCollapsed"
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    class="ml-2 p-2 text-[#8c7e6a] hover:text-[#8b2635] transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </Link>
            </div>
        </div>
    </aside>

    <!-- Mobile Navigation -->
    <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" leave-active-class="transition duration-300" leave-to-class="opacity-0">
        <div v-if="showingMobileMenu" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 lg:hidden" @click="$emit('close')"></div>
    </transition>

    <transition enter-active-class="transition duration-500 transform" enter-from-class="-translate-x-full" leave-active-class="transition duration-500 transform" leave-to-class="-translate-x-full">
        <aside v-if="showingMobileMenu" class="fixed inset-y-0 left-0 w-80 bg-[#1c1a18] z-[60] lg:hidden flex flex-col p-8 text-[#d9c5a3] shadow-2xl border-r border-[#3d352e]">
            <div class="h-20 flex items-center justify-between mb-8">
                <div class="flex items-center space-x-4">
                    <ApplicationLogo class="h-10 w-auto" />
                    <span class="text-2xl font-serif font-black italic tracking-tighter">OJT</span>
                </div>
                <button @click="$emit('close')" class="p-3 bg-white/[0.05] rounded-2xl text-[#8c7e6a] hover:text-[#d9c5a3] transition-all">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <nav class="flex-1 space-y-2">
                <Link v-for="item in menuItems" :key="item.name" href="#" class="flex items-center p-5 rounded-2xl bg-black/20 border border-[#3d352e] hover:bg-[#2d2a27] transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-6 text-[#8c7e6a] group-hover:text-[#8b2635] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <span class="font-bold text-[#d9c5a3] font-serif">{{ item.name }}</span>
                </Link>
            </nav>
        </aside>
    </transition>
</template>





