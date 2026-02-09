<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Login from './Auth/Login.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <Login />
</template>
