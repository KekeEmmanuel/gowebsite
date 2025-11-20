<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: String,
    rows: {
        type: Number,
        default: 3,
    },
});

const emit = defineEmits(['update:modelValue']);

const value = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<template>
    <textarea
        v-model="value"
        :rows="rows"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-safari-green focus:ring-safari-green"
    />
</template>

