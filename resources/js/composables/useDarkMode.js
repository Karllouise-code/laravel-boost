import { ref, onMounted } from 'vue';

// Global reactive state
const isDark = ref(false);

export function useDarkMode() {
    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
        updateDarkMode();
    };

    const updateDarkMode = () => {
        console.log('Updating dark mode:', isDark.value); // Debug log
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            console.log('Dark mode applied'); // Debug log
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
            console.log('Light mode applied'); // Debug log
        }
        console.log('HTML classes:', document.documentElement.className); // Debug log
    };

    const initializeDarkMode = () => {
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            isDark.value = true;
        } else {
            isDark.value = false;
        }
        
        updateDarkMode();
    };

    onMounted(() => {
        // Check current state from HTML element
        const currentIsDark = document.documentElement.classList.contains('dark');
        isDark.value = currentIsDark;

        // Watch for system theme changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleSystemThemeChange = (e) => {
            if (!localStorage.getItem('theme')) {
                isDark.value = e.matches;
                updateDarkMode();
            }
        };
        
        mediaQuery.addEventListener('change', handleSystemThemeChange);
    });

    return {
        isDark,
        toggleDarkMode,
        initializeDarkMode
    };
}