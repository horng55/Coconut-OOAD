import { ref, watch, onMounted } from 'vue';
import LocalStorageHelper from '../Helper/LocalStorageHelper.js';

const DARK_MODE_KEY = 'darkMode';
const isDark = ref(false);

// Initialize dark mode from localStorage (default to light mode)
const initDarkMode = () => {
    const saved = localStorage.getItem(DARK_MODE_KEY);
    if (saved !== null) {
        isDark.value = saved === 'true';
    } else {
        // Default to light mode (white)
        isDark.value = false;
    }
    applyDarkMode();
};

// Apply dark mode to document
const applyDarkMode = () => {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

// Toggle dark mode
const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    LocalStorageHelper.setValue(DARK_MODE_KEY, isDark.value.toString());
    applyDarkMode();
};

// Watch for system preference changes (disabled - always default to light)
const watchSystemPreference = () => {
    // System preference watching disabled - default to light mode
    // Users can manually toggle dark mode if needed
};

export function useDarkMode() {
    onMounted(() => {
        initDarkMode();
        watchSystemPreference();
    });

    // Watch for changes and persist
    watch(isDark, (newValue) => {
        LocalStorageHelper.setValue(DARK_MODE_KEY, newValue.toString());
        applyDarkMode();
    });

    return {
        isDark,
        toggleDarkMode,
    };
}

