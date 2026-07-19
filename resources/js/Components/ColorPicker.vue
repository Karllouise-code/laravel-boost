<template>
    <div class="relative" ref="root">
        <button
            @click.stop="toggle"
            class="w-7 h-7 rounded-full border-2 transition-all hover:scale-110"
            :style="{ background: modelValue, borderColor: open ? 'var(--color-text-primary)' : 'var(--color-border)' }"
        />
        <div
            v-if="open"
            @mousedown.stop
            class="absolute top-full left-1/2 -translate-x-1/2 mt-2 p-2.5 rounded-lg border shadow-lg z-50 w-44"
            :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }"
        >
            <div class="grid grid-cols-4 gap-1.5 mb-2">
                <button
                    v-for="color in presetColors"
                    :key="color"
                    @click.stop="selectColor(color)"
                    class="w-8 h-8 rounded-full border-2 transition-all hover:scale-110"
                    :style="{
                        background: color,
                        borderColor: modelValue === color ? 'var(--color-text-primary)' : 'transparent',
                    }"
                />
            </div>
            <div class="flex items-center gap-2 pt-1.5 border-t" :style="{ borderColor: 'var(--color-border)' }">
                <input
                    type="color"
                    :value="modelValue"
                    @input="onCustomColor"
                    @mousedown.stop
                    class="w-7 h-7 rounded cursor-pointer border-0 p-0"
                />
                <span class="text-[11px] tabular-nums" style="color: var(--color-text-muted);">{{ modelValue }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: String,
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const root = ref(null);
const presetColors = [
    '#6366f1', '#8b5cf6', '#ec4899', '#ef4444',
    '#f59e0b', '#10b981', '#06b6d4', '#64748b',
];

const toggle = () => {
    open.value = !open.value;
};

const selectColor = (color) => {
    emit('update:modelValue', color);
    open.value = false;
};

const onCustomColor = (e) => {
    emit('update:modelValue', e.target.value);
};

const handleMouseDown = (e) => {
    if (root.value && !root.value.contains(e.target)) {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('mousedown', handleMouseDown));
onUnmounted(() => document.removeEventListener('mousedown', handleMouseDown));
</script>
