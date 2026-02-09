<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-[2rem] border border-white/[0.05] bg-white/[0.02] px-8 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-200 shadow-xl transition duration-300 ease-in-out hover:bg-white/[0.05] hover:text-white hover:border-white/[0.1] hover:scale-105 focus:outline-none focus:ring-4 focus:ring-white/5 active:scale-95 disabled:opacity-25"
    >
        <slot />
    </button>
</template>

