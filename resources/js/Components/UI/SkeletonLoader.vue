<script setup>
defineProps({
    width: {
        type: String,
        default: '100%'
    },
    height: {
        type: String,
        default: '1rem'
    },
    borderRadius: {
        type: String,
        default: '0.25rem'
    },
    opacity: {
        type: String,
        default: '0.05'
    }
});
</script>

<template>
    <div 
        class="skeleton-aura"
        :style="{
            width: width,
            height: height,
            borderRadius: borderRadius,
        }"
    >
        <div class="shimmer"></div>
    </div>
</template>

<style scoped>
.skeleton-aura {
    position: relative;
    overflow: hidden;
    background: rgba(227, 213, 193, v-bind(opacity)); /* Using parchment-like color */
    border: 1px solid rgba(140, 106, 74, 0.05); /* Subtle brass border */
}

.shimmer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(140, 106, 74, 0.08),
        transparent
    );
    animation: shimmer 2s infinite cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}
</style>
