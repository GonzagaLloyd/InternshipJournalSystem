<script setup>
defineProps({
    label: String,
    value: [String, Number],
    icon: String,
    trend: String,
    trendColor: {
        type: String,
        default: 'text-green-600'
    }
});
</script>

<template>
    <div class="flex items-center p-8 bg-[#1c1f26] border border-white/[0.04] rounded-[2rem] shadow-2xl transition-all hover:bg-white/[0.02] hover:border-white/[0.08] group">
        <div v-if="$slots.icon" class="p-4 mr-6 bg-indigo-500/10 text-indigo-500 rounded-2xl group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/5">
            <slot name="icon" />
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 group-hover:text-slate-400 transition-colors">{{ label }}</p>
            <div class="flex items-baseline">
                <p class="text-3xl font-black text-white tracking-tighter">{{ value }}</p>
                <span v-if="trend" :class="['ml-3 text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg border border-white/5', trendColor]">
                    {{ trend }}
                </span>
            </div>
        </div>
    </div>
</template>
