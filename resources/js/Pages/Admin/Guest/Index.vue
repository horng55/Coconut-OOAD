<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";
import TextHelper from "../../../Helper/TextHelper.js";
import EventHelper from "../../../Helper/EventHelper.js";
import DateHelper from "../../../Helper/DateHelper.js";

const title = [{label: "Guests", href: route("admin.guests.index")}];

const props = defineProps({
    paginate: Object,
    search: String,
});

const searchQuery = ref(props.search || "");

const handleSearch = () => {
    router.get(route('admin.guests.index'), { search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

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
            title="Guest Management"
            :subtitle="`Total: ${paginate?.total || 0} guests from all users`"
            icon="fas fa-users"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-pink-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search guests..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    />
                </div>
            </template>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 border border-blue-500/20 rounded-xl p-4">
                    <div class="text-gray-600 dark:text-gray-400 text-sm mb-1">Total USD</div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ paginate?.total_usd?.toFixed(2) || '0.00' }}</div>
                </div>
                <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 border border-purple-500/20 rounded-xl p-4">
                    <div class="text-gray-600 dark:text-gray-400 text-sm mb-1">Total KHR</div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ TextHelper.getFormatRiel(paginate?.total_khr || 0) }}៛</div>
                </div>
                <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 border border-green-500/20 rounded-xl p-4">
                    <div class="text-gray-600 dark:text-gray-400 text-sm mb-1">Bank Transfer</div>
                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                        ${{ paginate?.bank?.usd?.toFixed(2) || '0.00' }} / {{ TextHelper.getFormatRiel(paginate?.bank?.khr || 0) }}៛
                    </div>
                </div>
                <div class="bg-gradient-to-br from-orange-500/10 to-orange-600/10 border border-orange-500/20 rounded-xl p-4">
                    <div class="text-gray-600 dark:text-gray-400 text-sm mb-1">Cash</div>
                    <div class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                        ${{ paginate?.cash?.usd?.toFixed(2) || '0.00' }} / {{ TextHelper.getFormatRiel(paginate?.cash?.khr || 0) }}៛
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-user mr-2 text-purple-500"></i>Guest Name
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-users mr-2 text-purple-500"></i>User
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-dollar-sign mr-2 text-purple-500"></i>Amount
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-credit-card mr-2 text-purple-500"></i>Payment Method
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-calendar mr-2 text-purple-500"></i>Event
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-comment mr-2 text-purple-500"></i>Description
                            </th>
                            <th class=" text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-cog mr-2 text-gray-500"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(item, index) in paginate?.data || []" 
                            :key="item.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-purple-500/10 dark:bg-purple-500/30 rounded-lg text-purple-500 dark:text-purple-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.invite_name || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.user_name || item.user_username || 'N/A' }}</span>
                                    <span v-if="item.user_email" class="text-gray-600 dark:text-gray-400 text-xs block">{{ item.user_email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="space-y-1">
                                    <span v-if="item.amount_usd" class="text-blue-600 dark:text-blue-400 font-bold block">${{ item.amount_usd }}</span>
                                    <span v-if="item.amount_khr" class="text-purple-600 dark:text-purple-400 font-bold block">{{ TextHelper.getFormatRiel(item.amount_khr) }}៛</span>
                                    <span v-if="!item.amount_usd && !item.amount_khr" class="text-gray-400">N/A</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium"
                                    :class="item.payment_option === 'bank' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' 
                                        : 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300'">
                                    <i :class="item.payment_option === 'bank' ? 'fas fa-university' : 'fas fa-money-bill-wave'"></i>
                                    {{ TextHelper.getBank(item.payment_option) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div>
                                    <span class="font-medium">{{ EventHelper.getEvents(item.event_type) }}</span>
                                    <span v-if="item.event_date" class="text-xs block text-gray-500">{{ DateHelper.dateFormat(item.event_date) }}</span>
                                    <span v-if="item.event_name" class="text-xs block text-gray-500">{{ item.event_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate" :title="item.description">
                                {{ item.description || 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.guests.show', item.id)"
                                        class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2"
                                        title="View Guest"
                                    >
                                        <i class="fas fa-eye"></i>
                                        View
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!paginate?.data || paginate.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No guests found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="paginate?.links" :links="paginate.links"/>
        </AdminPageWrapper>
    </App>
</template>

<style scoped>
</style>

