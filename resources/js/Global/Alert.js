import {defineStore} from "pinia";
import {computed, watch, reactive} from "vue";
import {usePage} from "@inertiajs/vue3";
import {v4 as uuidv4} from "uuid";
import {AlertError, AlertSuccess} from "../Utils/AlertUtil.js";

export const useAlertStore = defineStore('alert', () => {
    const alert = computed(() => usePage().props.flash.alert);
    const alerts = reactive([]);

    watch(alert, async (newAlert) => {
        if (newAlert && !newAlert?.id) {
            await addAlert(newAlert);
        }
    });

    const removeAlert = (id) => {
        setTimeout(() => {
            const index = alerts.findIndex(item => item.id === id);
            if (index !== -1) {
                alerts.splice(index, 1);
            }
        }, 3000);
    };

    const addAlert = async (alert) => {
        const id = uuidv4();
        usePage().props.flash.alert.id = id;
        alerts.push({id: id, ...alert});
        removeAlert(id);

        if (alerts.length > 5) {
            alerts.shift();
        }
        for (const newAlert of alerts) {
            await alertMessage(newAlert);
        }
    };

    const alertMessage = async (newAlert) => {
        const type = newAlert.type;
        const message = newAlert.message;
        if (type === 'success') {
            await AlertSuccess({message: message});
        } else if (type === 'danger') {
            await AlertError({message: message});
        }
    };
});
