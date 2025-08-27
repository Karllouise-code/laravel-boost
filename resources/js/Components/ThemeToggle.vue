<template>
    <button
        @click="toggleDarkMode"
        class="relative p-3 rounded-xl border-2 transition-all duration-300 hover:scale-105"
        :class="isDark 
            ? 'bg-gray-800 border-gray-700 text-yellow-400 hover:bg-gray-700' 
            : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm'"
        :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <!-- Sun Icon (Light Mode) -->
        <svg 
            v-if="!isDark"
            class="w-5 h-5 transition-transform duration-300"
            fill="currentColor" 
            viewBox="0 0 20 20"
        >
            <path 
                fill-rule="evenodd" 
                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" 
                clip-rule="evenodd"
            />
        </svg>

        <!-- Moon Icon (Dark Mode) -->
        <svg 
            v-else
            class="w-5 h-5 transition-transform duration-300"
            fill="currentColor" 
            viewBox="0 0 20 20"
        >
            <path 
                d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
            />
        </svg>

        <!-- Loading animation overlay -->
        <div 
            v-if="isToggling"
            class="absolute inset-0 flex items-center justify-center bg-current opacity-20 rounded-xl"
        >
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </button>
</template>

<script setup>
import { ref } from 'vue';
import { useDarkMode } from '@/composables/useDarkMode.js';

const { isDark, toggleDarkMode: toggle } = useDarkMode();
const isToggling = ref(false);

const toggleDarkMode = async () => {
    isToggling.value = true;
    
    // Add a small delay for smooth animation
    setTimeout(() => {
        toggle();
        isToggling.value = false;
    }, 150);
};
</script>