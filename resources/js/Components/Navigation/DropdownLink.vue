<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-[#C9B79C] transition duration-150 ease-in-out hover:bg-[#353535] focus:bg-[#353535] focus:outline-none font-serif tracking-wide"
    >
        <slot />
    </Link>
</template>

