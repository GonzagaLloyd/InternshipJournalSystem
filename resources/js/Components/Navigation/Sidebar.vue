<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';
import { ref } from 'vue';

const isLoggingOut = ref(false);
const isNavigating = ref(false);

const handleLogout = () => {
    isLoggingOut.value = true;
    setTimeout(() => {
        router.post(route('logout'), {}, {
            onFinish: () => isLoggingOut.value = false
        });
    }, 1500);
};

const navigateWithLoader = (routeName) => {
    if (routeName === '#' || route().current(routeName)) return;
    router.visit(route(routeName));
};

defineProps({
    showingMobileMenu: Boolean,
    isCollapsed: Boolean,
});

const user = usePage().props.auth.user;
const emit = defineEmits(['close']);

const menuItems = [
    { name: 'Home', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', routeName: 'dashboard' },
    { name: 'Entries', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', routeName: 'journal.index' },
    { name: 'Tasks', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', routeName: 'tasks.index' },
    { name: 'Calendar', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', routeName: 'calendar.index' },
];

const managementItems = [
    { name: 'Reports', icon: 'M9 17v-2a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm3 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM9 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm3 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2z', routeName: 'reports.index' },
    { name: 'Vault', icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', routeName: 'vault.index' },
];
</script>

<template>
    <!-- Desktop Sidebar -->
    <aside 
        :class="[
            isCollapsed ? 'w-24' : 'w-72',
            'hidden lg:flex flex-col border-r border-white/[0.03] bg-void fixed h-screen transition-all duration-300 ease-in-out z-40 text-cream shadow-2xl overflow-hidden'
        ]"
    >
        <!-- Logo Section -->
        <div :class="['h-28 flex items-center shrink-0 transition-all', isCollapsed ? 'justify-center w-full' : 'px-8']">
            <Link :href="route('dashboard')" :class="['flex items-center group', isCollapsed ? 'justify-center' : 'space-x-4']">
                <div class="flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                    <ApplicationLogo :class="[isCollapsed ? 'h-11' : 'h-16', 'w-auto opacity-90 group-hover:opacity-100 transition-all brightness-[0.9] sepia-[0.2]']" />
                </div>
                <span v-if="!isCollapsed" class="text-2xl font-cinzel font-black tracking-widest text-brass uppercase italic">OJT</span>
            </Link>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-4 scrollbar-hide">
            <div class="px-5 space-y-2">
                <Link 
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.routeName === '#' ? '#' : route(item.routeName)"
                    :class="[
                        route().current(item.routeName) ? 'bg-coal text-cream shadow-lg rounded-xl border border-brass/20' : 'text-brass/70 hover:bg-white/[0.03] hover:text-cream rounded-xl',
                        isCollapsed ? 'h-12 w-12 mx-auto justify-center p-0' : 'px-4 py-3.5',
                        'flex items-center transition-all duration-300 group relative overflow-hidden'
                    ]"
                >
                    <!-- Active Ambient Glow -->
                    <div v-if="route().current(item.routeName)" class="absolute inset-0 bg-brass/5 blur-xl pointer-events-none"></div>
                    
                    <div :class="[isCollapsed ? '' : 'mr-4']" class="relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" :class="route().current(item.routeName) ? 'scale-110 shadow-[0_0_10px_var(--color-accent-brass)]' : ''" class="h-6 w-6 transition-transform duration-500 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                    </div>
                    <span v-if="!isCollapsed" class="text-lg font-bold truncate font-cormorant tracking-wide relative z-10">{{ item.name }}</span>
                    
                    <!-- Feather Pointer Indicator -->
                    <div v-if="route().current(item.routeName) && !isCollapsed" class="absolute right-4 w-1.5 h-1.5 rounded-full bg-brass animate-pulse shadow-[0_0_8px_var(--color-accent-brass)]"></div>
                </Link>
            </div>

            <div class="my-6 border-t border-white/[0.05] mx-8" v-show="!isCollapsed"></div>

            <div class="px-5 space-y-2">
                <Link 
                    v-for="sub in managementItems"
                    :key="sub.name"
                    :href="sub.routeName === '#' ? '#' : route(sub.routeName)"
                    :class="[
                        route().current(sub.routeName) ? 'bg-coal text-cream shadow-lg rounded-xl border border-brass/20' : 'text-brass/70 hover:bg-white/[0.03] hover:text-cream rounded-xl',
                        isCollapsed ? 'h-12 w-12 mx-auto justify-center p-0' : 'px-4 py-3.5',
                        'flex items-center transition-all duration-300 group relative overflow-hidden'
                    ]"
                >
                    <!-- Active Ambient Glow -->
                    <div v-if="route().current(sub.routeName)" class="absolute inset-0 bg-brass/5 blur-xl pointer-events-none"></div>

                    <div :class="[isCollapsed ? '' : 'mr-4']" class="relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" :class="route().current(sub.routeName) ? 'scale-110' : ''" class="h-6 w-6 transition-transform duration-500 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="sub.icon" />
                        </svg>
                    </div>
                    <span v-if="!isCollapsed" class="text-lg font-bold truncate font-cormorant tracking-wide relative z-10">{{ sub.name }}</span>

                    <!-- Feather Pointer Indicator -->
                    <div v-if="route().current(sub.routeName) && !isCollapsed" class="absolute right-4 w-1.5 h-1.5 rounded-full bg-brass animate-pulse shadow-[0_0_8px_var(--color-accent-brass)]"></div>
                </Link>
            </div>
        </div>

        <!-- Footer / User Profile -->
        <div class="p-6 mt-auto border-t border-white/[0.05] bg-black/20">
            <div @click="handleLogout" :class="['flex items-center transition-all hover:bg-[#333333]/90 cursor-pointer group', isCollapsed ? 'justify-center h-10 w-10 mx-auto rounded-lg' : 'rounded-2xl border border-white/[0.05] p-3 shadow-md']">
                <div :class="[isCollapsed ? 'h-8 w-8' : 'h-10 w-10', 'bg-[#A68B6A] rounded-lg flex-shrink-0 flex items-center justify-center text-[#1B1B1B] font-black text-xs shadow-lg transform transition-transform group-hover:scale-105']">
                    {{ user.name.charAt(0) }}
                </div>
                <div v-if="!isCollapsed" class="ml-4 flex-1 min-w-0">
                    <p class="text-[14px] font-bold text-cream truncate tracking-tight font-cormorant">{{ user.name }}</p>
                    <p class="text-[10px] font-bold text-brass/80 truncate group-hover:text-cream transition-colors uppercase tracking-widest font-cinzel">Scribe Active</p>
                </div>
                <div 
                    v-if="!isCollapsed"
                    class="ml-2 p-2 text-[#A68B6A] group-hover:text-[#E3D5C1] transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Navigation -->
    <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" leave-active-class="transition duration-300" leave-to-class="opacity-0">
        <div v-if="showingMobileMenu" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 lg:hidden" @click="$emit('close')"></div>
    </transition>

    <transition enter-active-class="transition duration-500 transform" enter-from-class="-translate-x-full" leave-active-class="transition duration-500 transform" leave-to-class="-translate-x-full">
        <aside v-if="showingMobileMenu" class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-[#1B1B1B] z-[60] lg:hidden flex flex-col p-6 text-[#C9B79C] shadow-2xl border-r border-white/[0.05]">
            <div class="h-20 flex items-center justify-between mb-8">
                <div class="flex items-center space-x-4">
                    <ApplicationLogo class="h-10 w-auto" />
                    <span class="text-2xl font-cinzel font-black italic tracking-tighter">OJT</span>
                </div>
                <button @click="$emit('close')" class="p-3 bg-white/[0.05] rounded-xl text-[#8C6A4A] hover:text-[#C9B79C] transition-all">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <nav class="flex-1 space-y-2 overflow-y-auto pr-2 scrollbar-hide">
                <Link 
                    v-for="item in menuItems" 
                    :key="item.name" 
                    :href="item.routeName === '#' ? '#' : route(item.routeName)" 
                    class="flex items-center p-4 rounded-xl bg-black/20 border border-white/[0.05] hover:bg-[#353535] transition-all group"
                    @click="$emit('close')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 text-[#8C6A4A]/60 group-hover:text-[#8C6A4A] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <span class="font-bold text-lg text-[#C9B79C] font-cormorant transition-colors group-hover:text-white">{{ item.name }}</span>
                </Link>

                <div class="my-6 border-t border-white/[0.05]"></div>

                <Link 
                    v-for="sub in managementItems"
                    :key="sub.name"
                    :href="sub.routeName === '#' ? '#' : route(sub.routeName)"
                    class="flex items-center p-4 rounded-xl bg-black/20 border border-white/[0.05] hover:bg-[#353535] transition-all group"
                    @click="$emit('close')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 text-[#8C6A4A]/60 group-hover:text-[#8C6A4A] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="sub.icon" />
                    </svg>
                    <span class="font-bold text-lg text-[#C9B79C] font-cormorant transition-colors group-hover:text-white">{{ sub.name }}</span>
                </Link>
            </nav>

            <!-- Mobile Footer / User Profile -->
            <div class="mt-8 border-t border-white/[0.05] pt-8">
                <div @click="handleLogout" class="flex items-center p-4 rounded-2xl bg-black/20 border border-white/[0.05] relative overflow-hidden group hover:bg-[#333333]/90 cursor-pointer transition-all shadow-md">
                    <div class="h-10 w-10 bg-[#8C6A4A] rounded-lg flex-shrink-0 flex items-center justify-center text-[#1B1B1B] font-black text-xs shadow-lg transform transition-transform group-hover:scale-105">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <p class="text-sm font-black text-[#C9B79C] truncate font-cormorant">{{ user.name }}</p>
                        <p class="text-[10px] font-bold text-[#8C6A4A]/80 truncate group-hover:text-[#C9B79C] uppercase tracking-widest font-cinzel transition-colors">Scribe Active</p>
                    </div>
                    <div 
                        class="p-2 text-[#8C6A4A]/60 group-hover:text-[#C9B79C] transition-colors"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </div>
                </div>
            </div>
        </aside>
    </transition>

    <TomeLoader :show="isLoggingOut" message="Retiring to Chambers..." />
</template>





