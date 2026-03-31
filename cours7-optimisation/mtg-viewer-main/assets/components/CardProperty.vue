<script setup>
defineProps({
    name: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        default: null,
    },
});
</script>

<template>
    <div class="card-property">
        <div class="name">{{ name }} : </div>
        <div class="value">{{ value ?? 'N/A' }}</div>
    </div>
</template>
