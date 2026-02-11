<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#8C6A4A] text-sm font-bold leading-5 text-[#C9B79C] focus:outline-none focus:border-[#C9B79C] transition duration-150 ease-in-out font-serif uppercase tracking-widest'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-[#8C6A4A]/60 hover:text-[#C9B79C] hover:border-[#8C6A4A]/20 focus:outline-none focus:text-[#C9B79C] focus:border-[#C9B79C] transition duration-150 ease-in-out font-serif uppercase tracking-widest',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

