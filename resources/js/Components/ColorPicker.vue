<template>
    <div class="relative">
        <button
            @click="open = !open"
            class="w-8 h-8 rounded-full border-2 transition-all hover:scale-110"
            :style="{ background: modelValue, borderColor: 'var(--color-border)' }"
        />
        <div
            v-if="open"
            class="absolute top-full left-0 mt-2 p-3 rounded-lg border shadow-lg z-50"
            :style="{ background: 'var(--color-surface)', borderColor: 'var(--color-border)' }"
        >
            <div class="grid grid-cols-4 gap-2 mb-3">
                <button
                    v-for="color in presetColors"
                    :key="color"
                    @click="selectColor(color)"
                    class="w-8 h-8 rounded-full border-2 transition-all hover:scale-110"
                    :style="{
                        background: color,
                        borderColor: modelValue === color ? 'var(--color-text-primary)' : 'transparent',
                    }"
                />
            </div>
            <input
                type="color"
                :value="modelValue"
                @input="selectColor($event.target.value)"
                class="w-full h-8 rounded cursor-pointer"
            />
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
const presetColors = [
    '#6366f1', '#8b5cf6', '#ec4899', '#ef4444',
    '#f59e0b', '#10b981', '#06b6d4', '#64748b',
];

const selectColor = (color) => {
    emit('update:modelValue', color);
    open.value = false;
};

const handleClickOutside = (e) => {
    if (!e.target.closest('.relative')) {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>
