<script setup>
const props = defineProps({
    selectedDay: Object
});

const emit = defineEmits(['navigate', 'clear']);

const formatDate = (dateString) => {
    return new Intl.DateTimeFormat('en-US', { day: 'numeric', month: 'long' }).format(new Date(dateString));
};
</script>

<template>
    <aside class="w-full lg:w-96 shrink-0 flex flex-col h-full overflow-hidden">
        <div class="flex-1 bg-transparent border-l border-[#3d3d3d]/20 px-8 lg:px-12 py-2 flex flex-col overflow-hidden">
            
            <div class="relative z-10 flex flex-col h-full overflow-hidden">
                <!-- Selected Header -->
                <div v-if="selectedDay" class="mb-12 shrink-0">
                    <span class="text-[10px] uppercase tracking-[0.4em] text-[#8C6A4A] font-black font-serif">Daily Record</span>
                    <h2 class="text-3xl font-cinzel font-bold text-[#C9B79C] tracking-tight mt-2">
                        {{ formatDate(selectedDay.fullDate) }}
                    </h2>
                    <div class="h-[1px] w-12 bg-gradient-to-r from-[#8C6A4A]/60 to-transparent mt-6"></div>
                </div>
                
                <!-- Nothing Selected / Home State -->
                <div v-else class="flex-1 flex flex-col items-center justify-center text-center opacity-20 py-20">
                    <svg class="w-16 h-16 mb-4 text-[#8C6A4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="font-cinzel tracking-[0.3em] text-[10px] uppercase">Peering into Time</p>
                </div>

                <!-- Selected Day Items (Scrollable) -->
                <div v-if="selectedDay" class="flex-1 overflow-y-auto pr-4 scrollbar-hide space-y-12 min-h-0">
                    <div v-if="selectedDay.items?.length === 0" class="py-20 text-center opacity-20 italic text-xs font-serif tracking-widest">
                        The records are silent on this day.
                    </div>
                    
                    <div 
                        v-for="item in selectedDay.items" 
                        :key="item.id"
                        @click="emit('navigate', item)"
                        class="group/item cursor-pointer relative"
                    >
                        <!-- Vertical Line Aura -->
                        <div class="absolute -left-6 top-1 bottom-1 w-[2px] bg-[#8C6A4A]/10 group-hover/item:bg-[#8C6A4A]/60 transition-all duration-500"></div>
                        
                        <div class="flex items-center gap-4 mb-2 opacity-80 group-hover/item:opacity-100 transition-opacity">
                            <span class="text-[10px] uppercase tracking-[0.3em] text-[#8C6A4A] font-black font-serif">
                                {{ item.type === 'entry' ? 'Lore Entry' : 'Decree Task' }}
                            </span>
                        </div>
                        
                        <h4 class="text-[#C9B79C] font-serif text-[20px] leading-snug group-hover:translate-x-1 transition-all duration-500">
                            {{ item.title }}
                        </h4>
                        
                        <div v-if="item.completed" class="mt-2 flex items-center gap-2 text-[#8C6A4A] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span class="text-[10px] uppercase font-black tracking-widest">Fulfilled</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Stats (Selected) -->
                <div v-if="selectedDay" class="mt-auto pt-8 border-t border-white/5 flex items-center justify-between text-[10px] font-black uppercase tracking-[0.3em] text-[#8C6A4A]/60 shrink-0">
                    <span>{{ selectedDay.items.length }} Chronicles</span>
                    <span class="hover:text-[#C9B79C] cursor-pointer transition-colors" @click="emit('clear')">Collapse</span>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
