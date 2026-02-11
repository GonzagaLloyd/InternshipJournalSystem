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
        class="inline-flex items-center justify-center rounded-[2rem] border border-white/5 bg-white/[0.02] px-8 py-3 text-[10px] font-black uppercase tracking-[0.3em] text-[#C9B79C]/60 shadow-xl transition duration-300 ease-in-out hover:bg-white/[0.05] hover:text-[#C9B79C] hover:border-white/10 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-white/5 active:scale-95 disabled:opacity-25 font-serif"
    >
        <slot />
    </button>
</template>

