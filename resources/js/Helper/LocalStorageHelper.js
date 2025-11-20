export default {
    getBooleanValue(key) {
        const value = localStorage.getItem(key);
        if (value == null) {
            return true;
        }

        return value === 'true';
    },

    getValue(key, defaultValue = null) {
        const value = localStorage.getItem(key);
        return value !== null ? value : defaultValue;
    },

    setValue(key, value) {
        localStorage.setItem(key, value);
    }
}
