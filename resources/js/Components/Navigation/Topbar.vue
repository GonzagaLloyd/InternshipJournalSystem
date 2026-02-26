<script setup>
defineProps({
    title: String,
    subtitle: String,
    isCollapsed: Boolean,
});

defineEmits(['toggle-sidebar', 'open-mobile-menu']);
</script>

<template>
    <header class="sticky top-0 h-16 sm:h-20 flex items-center justify-between px-4 sm:px-6 lg:px-12 z-30 shrink-0 bg-void/90 backdrop-blur-xl border-b border-coal/20 shadow-2xl">
        <div class="flex items-center gap-3 sm:gap-8 min-w-0 pr-2">
            <div class="flex items-center">
                <!-- Mobile Menu Button -->
                <button 
                    @click="$emit('open-mobile-menu')" 
                    type="button" 
                    class="lg:hidden p-2 rounded-xl text-umber hover:text-parchment transition-all shrink-0"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Sidebar Toggle Button -->
                <button 
                    @click="$emit('toggle-sidebar')"
                    type="button"
                    class="hidden lg:flex p-2 rounded-xl text-umber/60 hover:text-parchment transition-all focus:outline-none shrink-0"
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

            <!-- Dynamic Page Title (WIP Aesthetic) -->
            <div v-if="title" class="flex flex-col min-w-0 truncate">
                <h2 class="text-[15px] sm:text-lg md:text-2xl font-cinzel font-bold text-[#E3D5C1] tracking-[0.05em] uppercase leading-tight md:leading-none truncate">
                    <span v-html="title"></span>
                </h2>
                <div class="flex items-center gap-2 sm:gap-3 mt-0.5 sm:mt-1.5">
                    <div class="hidden sm:block h-[1px] w-3 sm:w-4 bg-brass/40 shrink-0"></div>
                    <p v-if="subtitle" class="text-[8px] sm:text-[10px] md:text-[11px] uppercase tracking-[0.2em] sm:tracking-[0.4em] text-brass font-black truncate">
                        {{ subtitle }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-8">
            <!-- Bell -->
            <button class="relative text-[#8C6A4A]/60 hover:text-[#C9B79C] transition-all hover:scale-110 active:scale-95">
                <span class="absolute -top-1 -right-1 h-3 w-3 bg-[#525947] rounded-full border-2 border-[#1B1B1B] shadow-[0_0_8px_#525947]"></span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </button>
        </div>
    </header>
</template>
