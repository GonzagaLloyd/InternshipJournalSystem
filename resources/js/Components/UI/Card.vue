<script setup>
defineProps({
    title: String,
});
</script>

<template>
    <div class="overflow-hidden bg-[#1c1f26] sm:rounded-3xl border border-white/[0.04] shadow-2xl transition-all hover:border-white/[0.08] group">
        <div v-if="title || $slots.header" class="border-b border-white/[0.04] px-8 py-5 bg-white/[0.01]">
            <slot name="header">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] group-hover:text-white transition-colors">{{ title }}</h3>
            </slot>
        </div>
        <div class="p-8">
            <slot />
        </div>
        <div v-if="$slots.footer" class="border-t border-white/[0.04] bg-white/[0.01] px-8 py-4">
            <slot name="footer" />
        </div>
    </div>
</template>
