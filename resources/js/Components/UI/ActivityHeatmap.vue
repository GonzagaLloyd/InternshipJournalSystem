<script setup>
import { computed } from 'vue';

const props = defineProps({
    activity: {
        type: Object,
        default: () => ({})
    }
});

// Generate last 12 months for the heatmap labels
const months = computed(() => {
    const monthsArr = [];
    const date = new Date();
    for (let i = 11; i >= 0; i--) {
        const d = new Date(date.getFullYear(), date.getMonth() - i, 1);
        monthsArr.push(d.toLocaleString('default', { month: 'short' }));
    }
    return monthsArr;
});

// Generate grid data (53 weeks x 7 days)
const heatmapData = computed(() => {
    const data = [];
    const today = new Date();
    const startDate = new Date();
    startDate.setDate(today.getDate() - 364); // Last 365 days
    
    // Adjust to starting Sunday/Monday of that week
    const startDay = startDate.getDay();
    startDate.setDate(startDate.getDate() - startDay);

    for (let i = 0; i < 53; i++) {
        const week = [];
        for (let j = 0; j < 7; j++) {
            const current = new Date(startDate);
            current.setDate(startDate.getDate() + (i * 7) + j);
            
            const dateStr = current.toISOString().split('T')[0];
            const count = props.activity[dateStr] || 0;
            
            week.push({
                date: dateStr,
                count,
                level: count >= 5 ? 4 : count >= 3 ? 3 : count >= 2 ? 2 : count >= 1 ? 1 : 0,
                isFuture: current > today
            });
        }
        data.push(week);
    }
    return data;
});

const getIntensityClass = (level) => {
    switch (level) {
        case 1: return 'bg-umber/20 shadow-[0_0_5px_var(--color-accent-umber)]/10';
        case 2: return 'bg-umber/40 shadow-[0_0_8px_var(--color-accent-umber)]/20';
        case 3: return 'bg-umber/70 shadow-[0_0_12px_var(--color-accent-umber)]/30';
        case 4: return 'bg-parchment shadow-[0_0_15px_var(--color-text-parchment)]/40';
        default: return 'bg-white/5';
    }
};

</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-[10px] uppercase tracking-[0.4em] text-umber font-black">
                Lore Continuity <span class="text-parchment/40 font-serif lowercase italic tracking-normal ml-2">(Last 365 cycles)</span>
            </h4>
            <div class="flex items-center gap-2">
                <span class="text-[8px] uppercase tracking-widest text-umber/40">Void</span>
                <div class="flex gap-1">
                    <div v-for="i in 5" :key="i" :class="getIntensityClass(i-1)" class="w-2 h-2 rounded-[1px]"></div>
                </div>
                <span class="text-[8px] uppercase tracking-widest text-parchment/60">Full Bloom</span>
            </div>
        </div>

        <div class="relative bg-black/20 rounded-sm p-4 border border-white/5 overflow-hidden">
            <!-- Grid Scroll Container -->
            <div class="overflow-x-auto scrollbar-hide">
                <div class="flex gap-[3px] min-w-max pb-2">
                    <div v-for="(week, wIdx) in heatmapData" :key="wIdx" class="flex flex-col gap-[3px]">
                        <div 
                            v-for="(day, dIdx) in week" 
                            :key="dIdx"
                            class="w-[10px] h-[10px] rounded-[1.5px] transition-all duration-700 hover:scale-125 hover:z-10 group relative"
                            :class="[
                                day.isFuture ? 'opacity-0' : getIntensityClass(day.level)
                            ]"
                        >
                            <!-- Tooltip -->
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50">
                                <div class="bg-void border border-umber/40 px-2 py-1 rounded-sm whitespace-nowrap shadow-2xl">
                                    <p class="text-[9px] font-cinzel text-parchment">
                                        {{ day.count }} Fragments · {{ new Date(day.date).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                    </p>
                                </div>
                                <div class="w-1.5 h-1.5 bg-void border-r border-b border-umber/40 rotate-45 mx-auto -mt-[5px]"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Month Labels -->
            <div class="flex justify-between mt-2 px-1">
                <span v-for="month in months" :key="month" class="text-[8px] uppercase tracking-tighter text-umber/40 font-cinzel">
                    {{ month }}
                </span>
            </div>

            <!-- Ritual Aura -->
            <div class="absolute inset-0 pointer-events-none opacity-20 bg-gradient-to-tr from-umber/5 via-transparent to-transparent"></div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
