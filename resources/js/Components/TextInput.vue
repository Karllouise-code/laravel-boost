<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

const onFocus = () => {
    if (input.value) {
        input.value.style.borderColor = 'var(--color-accent)';
        input.value.style.boxShadow = '0 0 0 3px var(--color-accent-bg)';
    }
};

const onBlur = () => {
    if (input.value) {
        input.value.style.borderColor = 'var(--color-border)';
        input.value.style.boxShadow = 'none';
    }
};

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full rounded-lg px-4 py-3 text-sm transition-all duration-200 placeholder:text-sm"
        v-model="model"
        ref="input"
        :style="{
            border: '1.5px solid var(--color-border)',
            background: 'var(--color-card)',
            color: 'var(--color-text-primary)',
            outline: 'none',
        }"
        @focus="onFocus"
        @blur="onBlur"
    />
</template>
