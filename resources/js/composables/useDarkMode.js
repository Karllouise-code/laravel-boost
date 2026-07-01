import { ref, onMounted } from 'vue';

const theme = ref('coffee');

export function useDarkMode() {
    const toggleTheme = () => {
        theme.value = theme.value === 'coffee' ? 'nes' : 'coffee';
        applyTheme();
    };

    const applyTheme = () => {
        document.documentElement.setAttribute('data-theme', theme.value);
        localStorage.setItem('theme', theme.value);
    };

    const initializeTheme = () => {
        const saved = localStorage.getItem('theme');
        if (saved === 'coffee' || saved === 'nes') {
            theme.value = saved;
        } else {
            theme.value = 'coffee';
        }
        applyTheme();
    };

    onMounted(() => {
        const current = document.documentElement.getAttribute('data-theme');
        if (current === 'coffee' || current === 'nes') {
            theme.value = current;
        }
    });

    return {
        theme,
        toggleTheme,
        initializeTheme,
    };
}
