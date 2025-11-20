<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import TextHelper from "../../../Helper/TextHelper.js";
import EventHelper from "../../../Helper/EventHelper.js";
import DateHelper from "../../../Helper/DateHelper.js";

const props = defineProps({
    guest: Object,
});

const title = [
    {label: "Guests", href: route("admin.guests.index")},
    {label: "Guest Details", href: route("admin.guests.show", props.guest?.id)}
];

const formatAmount = (usd, khr) => {
    let parts = [];
    if (usd) parts.push(`$${usd}`);
    if (khr) parts.push(`${TextHelper.getFormatRiel(khr)}៛`);
    return parts.join(' / ') || '0';
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Guest Details"
            :subtitle="`Viewing guest information`"
            icon="fas fa-user"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-pink-500/20"
        >
            <template #actions>
                <Link
                    :href="route('admin.guests.index')"
                    class="flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-xl font-medium shadow-md transition-all duration-300 transform hover:scale-105"
                >
                    <i class="fas fa-arrow-left"></i>
                    Back to List
                </Link>
            </template>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 space-y-6">
                <!-- Guest Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-purple-500"></i>
                        Guest Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Guest Name</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ guest.invite?.name || guest.invite_name || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Description</label>
                            <p class="text-gray-800 dark:text-white">{{ guest.description || 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-money-bill-wave text-green-500"></i>
                        Payment Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Amount (USD)</label>
                            <p class="text-gray-800 dark:text-white font-bold text-lg text-blue-600 dark:text-blue-400">
                                ${{ guest.amount_usd || '0.00' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Amount (KHR)</label>
                            <p class="text-gray-800 dark:text-white font-bold text-lg text-purple-600 dark:text-purple-400">
                                {{ TextHelper.getFormatRiel(guest.amount_khr || 0) }}៛
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Payment Method</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium"
                                    :class="guest.payment_option === 'bank' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' 
                                        : 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300'">
                                    <i :class="guest.payment_option === 'bank' ? 'fas fa-university' : 'fas fa-money-bill-wave'"></i>
                                    {{ TextHelper.getBank(guest.payment_option) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Event Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6" v-if="guest.event">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-500"></i>
                        Event Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Event Type</label>
                            <p class="text-gray-800 dark:text-white font-medium">
                                {{ EventHelper.getEvents(guest.event.type) }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Event Name</label>
                            <p class="text-gray-800 dark:text-white">{{ guest.event.name || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Event Date</label>
                            <p class="text-gray-800 dark:text-white">{{ DateHelper.dateFormat(guest.event.date) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Location</label>
                            <p class="text-gray-800 dark:text-white">{{ guest.event.location || 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="pb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-users text-indigo-500"></i>
                        User Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Username</label>
                            <p class="text-gray-800 dark:text-white font-medium">{{ guest.user_username || 'N/A' }}</p>
                        </div>
                        <div v-if="guest.user">
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">User Name</label>
                            <p class="text-gray-800 dark:text-white">{{ guest.user.name || 'N/A' }}</p>
                        </div>
                        <div v-if="guest.user?.email">
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Email</label>
                            <p class="text-gray-800 dark:text-white">{{ guest.user.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <label class="font-semibold">Created At</label>
                            <p>{{ guest.created_at ? new Date(guest.created_at).toLocaleString() : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="font-semibold">Updated At</label>
                            <p>{{ guest.updated_at ? new Date(guest.updated_at).toLocaleString() : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

<style scoped>
</style>

