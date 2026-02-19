<script setup>
import { computed } from 'vue';
import SkeletonLoader from '@/Components/UI/SkeletonLoader.vue';

const props = defineProps({
    days: Array,
    selectedDay: Object,
    todayDate: String,
    loading: Boolean
});

const emit = defineEmits(['selectDay']);
</script>

<template>
    <div class="flex-1 min-w-0 flex flex-col h-full overflow-hidden">
        <!-- Weekday Headers (Fixed) -->
        <div class="grid grid-cols-7 mb-6 border-b border-[#3d3d3d]/40 pb-4 shrink-0">
            <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="text-center">
                <span class="text-[10px] font-cinzel uppercase tracking-[0.4em] text-[#8C6A4A] font-black opacity-90">{{ day }}</span>
            </div>
        </div>

        <!-- The Grid (Scrollable) -->
        <div class="flex-1 overflow-y-auto scrollbar-hide">
            <div class="grid grid-cols-7 gap-px bg-transparent min-h-full">
                <div 
                    v-for="(day, index) in days" 
                    :key="index"
                    @click="emit('selectDay', day)"
                    :class="[
                        day.month === 'current' ? 'bg-[#1B1B1B]/25 cursor-pointer hover:bg-[#1B1B1B]/40 border border-[#3d3d3d]/15 hover:border-[#8C6A4A]/30' : 'bg-transparent opacity-10 cursor-default border border-transparent',
                        day.fullDate === todayDate ? 'ring-1 ring-inset ring-[#8C6A4A]/50' : '',
                        selectedDay?.fullDate === day.fullDate ? 'bg-[#8C6A4A]/10 border-[#8C6A4A]/40' : '',
                        'min-h-[80px] md:min-h-[140px] p-2 md:p-3 transition-all duration-500 flex flex-col items-start gap-1 relative group/cell overflow-hidden backdrop-blur-sm'
                    ]"
                >
                    <template v-if="props.loading && day.month === 'current'">
                        <div class="w-full flex justify-between mb-4">
                            <SkeletonLoader width="1.5rem" height="1.2rem" />
                            <div class="flex gap-1">
                                <SkeletonLoader width="4px" height="4px" borderRadius="50%" />
                                <SkeletonLoader width="4px" height="4px" borderRadius="50%" />
                            </div>
                        </div>
                        <div class="flex-1 w-full space-y-2">
                            <SkeletonLoader width="80%" height="0.6rem" opacity="0.03" />
                            <SkeletonLoader width="60%" height="0.6rem" opacity="0.03" />
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex justify-between w-full items-start">
                            <span 
                                :class="[
                                    day.month === 'current' ? 'text-[#C9B79C]' : 'text-[#8C6A4A]/40',
                                    day.fullDate === todayDate ? 'text-white' : '',
                                    'text-sm md:text-base font-cinzel font-bold z-10'
                                ]"
                            >{{ day.day }}</span>
                            <div v-if="day.items?.length > 0" class="flex gap-1 pt-1">
                                <div 
                                    v-for="(item, idx) in day.items.slice(0, 2)" 
                                    :key="idx"
                                    class="w-1 h-1 rounded-full bg-[#8C6A4A]/60 shadow-[0_0_5px_#8C6A4A]"
                                ></div>
                            </div>
                        </div>

                        <!-- Tiny Preview -->
                        <div class="hidden md:flex flex-col gap-1 w-full mt-1.5">
                            <div 
                                v-for="item in day.items?.slice(0, 2)" 
                                :key="item.id || item._id"
                                class="text-[9px] uppercase tracking-wider font-serif truncate text-[#C9B79C]/70 group-hover/cell:text-[#C9B79C] transition-colors"
                            >
                                {{ item.title || item.name }}
                            </div>
                            <div v-if="day.items?.length > 2" class="text-[8px] text-[#8C6A4A] italic font-bold">+{{ day.items.length - 2 }} more</div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
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
