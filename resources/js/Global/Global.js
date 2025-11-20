import {defineStore} from "pinia";
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import LocalStorageHelper from "../Helper/LocalStorageHelper.js";
import {router} from "@inertiajs/vue3";

export const useGlobalStore = defineStore("global", () => {

    const currentUser = computed(() => usePage().props.current_user);
    const currentAdmin = computed(() => usePage().props.current_admin);
    const admin_sidebars = computed(() => usePage().props.admin_sidebars);
    const user_sidebars = computed(() => usePage().props.user_sidebars);
    const showMenu = ref((LocalStorageHelper.getBooleanValue('showMenu')));
    const locale = ref(LocalStorageHelper.getValue('locale') || 'en');

    function toggleMenu() {
        showMenu.value = !showMenu.value;
        LocalStorageHelper.setValue('showMenu', showMenu.value.toString());
    }

    async function changeLanguage(lang) {
        locale.value = lang;
        LocalStorageHelper.setValue('locale', lang);
        // Reload the page to apply language changes
        router.reload({ only: [] });
    }

    return {
        currentAdmin,
        currentUser,
        admin_sidebars,
        user_sidebars,
        showMenu,
        toggleMenu,
        locale,
        changeLanguage,
    };
});
