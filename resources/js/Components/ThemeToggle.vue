<template>
    <div class="flex rounded-lg border border-default overflow-hidden" style="border-color: var(--color-border);">
        <button
            @click="setTheme('coffee')"
            class="px-3 py-1.5 text-xs font-medium transition-colors duration-150"
            :style="{
                background: theme === 'coffee' ? 'var(--color-accent)' : 'transparent',
                color: theme === 'coffee' ? 'var(--color-accent-text)' : 'var(--color-text-muted)',
            }"
        >
            ☕ Coffee
        </button>
        <button
            @click="setTheme('nes')"
            class="px-3 py-1.5 text-xs font-medium transition-colors duration-150"
            :style="{
                background: theme === 'nes' ? 'var(--color-accent)' : 'transparent',
                color: theme === 'nes' ? 'var(--color-accent-text)' : 'var(--color-text-muted)',
            }"
        >
            🎮 NES
        </button>
    </div>
</template>

<script setup>
import { useDarkMode } from '@/composables/useDarkMode.js';

const { theme, toggleTheme } = useDarkMode();

const setTheme = (t) => {
    if (theme.value !== t) {
        theme.value = t;
        document.documentElement.setAttribute('data-theme', t);
        localStorage.setItem('theme', t);
    }
};
</script>
