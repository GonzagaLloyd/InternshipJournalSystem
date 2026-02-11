<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DialGrid from './DialGrid.vue';
import DialSidePanel from './DialSidePanel.vue';
import TomeLoader from '@/Components/UI/TomeLoader.vue';

const props = defineProps({
    events: Object
});

const isNavigating = ref(false);
const currentDate = ref(new Date());
const selectedDay = ref(null);

const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

const currentMonthName = computed(() => months[currentDate.value.getMonth()]);
const currentYear = computed(() => currentDate.value.getFullYear());

const daysInMonth = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    
    const days = [];
    
    // Previous month padding
    const prevLastDate = new Date(year, month, 0).getDate();
    for (let i = firstDay; i > 0; i--) {
        days.push({ 
            day: prevLastDate - i + 1, 
            month: 'prev', 
            fullDate: null 
        });
    }
    
    // Current month
    for (let i = 1; i <= lastDate; i++) {
        const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({ 
            day: i, 
            month: 'current', 
            fullDate: fullDate,
            items: props.events[fullDate] || []
        });
    }
    
    // Future padding
    const remaining = 42 - days.length;
    for (let i = 1; i <= remaining; i++) {
        days.push({ 
            day: i, 
            month: 'next', 
            fullDate: null 
        });
    }
    
    return days;
});

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const selectDay = (day) => {
    if (day.month === 'current') {
        selectedDay.value = day;
    }
};

const navigateToItem = (item) => {
    isNavigating.value = true;
    const routeName = item.type === 'entry' ? 'journal.show' : 'tasks.index';
    setTimeout(() => {
        router.visit(route(routeName, item.id));
    }, 1200);
};

const todayDate = new Date().toISOString().split('T')[0];
</script>

<template>
    <div class="flex flex-col h-full overflow-hidden">
        <!-- Calendar Controls -->
        <div class="relative z-10 mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 shrink-0">
            <div class="group">
                <div class="flex items-center gap-4 mb-2">
                    <div class="h-[1px] w-8 bg-[#8C6A4A]/40"></div>
                    <span class="text-[9px] uppercase tracking-[0.6em] text-[#8C6A4A] font-black">Celestial Alignment</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-cinzel font-bold text-[#C9B79C] tracking-tighter">
                    The <span class="italic opacity-80">Chronos</span> Dial
                </h1>
            </div>

            <div class="flex items-center bg-[#2D2D2D]/40 border border-white/5 p-2 rounded-2xl backdrop-blur-sm shadow-xl shrink-0">
                <button @click="prevMonth" class="p-3 text-[#8C6A4A] hover:text-[#C9B79C] transition-colors group">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="px-6 min-w-[180px] text-center">
                    <span class="text-xs font-cinzel font-black uppercase tracking-[0.4em] text-[#C9B79C]">{{ currentMonthName }}</span>
                    <p class="text-[9px] text-[#8C6A4A] font-serif uppercase tracking-[0.2em] font-bold mt-0.5">{{ currentYear }}</p>
                </div>
                <button @click="nextMonth" class="p-3 text-[#8C6A4A] hover:text-[#C9B79C] transition-colors group">
                    <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col lg:flex-row gap-12 relative z-10 min-h-0 overflow-hidden">
            <DialGrid 
                :days="daysInMonth" 
                :selected-day="selectedDay" 
                :today-date="todayDate"
                @select-day="selectDay"
            />

            <DialSidePanel 
                :selected-day="selectedDay"
                @navigate="navigateToItem"
                @clear="selectedDay = null"
            />
        </div>

        <TomeLoader :show="isNavigating" message="Consulting the Archives..." />
    </div>
</template>
