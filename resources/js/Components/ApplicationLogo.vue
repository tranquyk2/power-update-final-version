<template>
    <img
        src="/images/logo.jpg"
        alt=""
        :style="`width: ${width}px; height: ${height}px`"
    />
</template>

<script setup>
defineProps({
    width: {
        type: Number,
        default: 290,
    },
    height: {
        type: Number,
        default: 100,
    },
});
</script>
