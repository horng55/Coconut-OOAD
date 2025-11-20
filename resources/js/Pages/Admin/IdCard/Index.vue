<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "ID Cards", href: route("admin.id-cards.index")}];

const props = defineProps({
    idCards: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedCardType = ref(props.filters?.card_type || "");
const selectedStatus = ref(props.filters?.status || "");

const handleSearch = () => {
    router.get(route('admin.id-cards.index'), {
        search: searchQuery.value,
        card_type: selectedCardType.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteIdCard = (id) => {
    if (confirm('Are you sure you want to delete this ID card?')) {
        router.delete(route('admin.id-cards.destroy', id));
    }
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="ID Card Management"
            :subtitle="`Total: ${idCards?.total || 0} ID cards`"
            icon="fas fa-id-card"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-pink-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search by card number or name..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    />
                    <select
                        v-model="selectedCardType"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    >
                        <option value="">All Types</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="revoked">Revoked</option>
                    </select>
                    <Link
                        :href="route('admin.id-cards.create', { type: 'student' })"
                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Add ID Card
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Card Number</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Owner</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Issue Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Expiry Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(idCard, index) in idCards?.data || []" 
                            :key="idCard.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-purple-500/10 dark:bg-purple-500/30 rounded-lg text-purple-500 dark:text-purple-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-medium font-mono">{{ idCard.card_number || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    idCard.card_type === 'student' 
                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
                                ]">
                                    <i :class="idCard.card_type === 'student' ? 'fas fa-user-graduate' : 'fas fa-chalkboard-teacher'"></i>
                                    {{ idCard.card_type ? idCard.card_type.charAt(0).toUpperCase() + idCard.card_type.slice(1) : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">
                                        {{ idCard.card_type === 'student' 
                                            ? (idCard.student?.user?.full_name || 'N/A')
                                            : (idCard.teacher?.user?.full_name || 'N/A')
                                        }}
                                    </span>
                                    <span v-if="idCard.card_type === 'student' && idCard.student?.student_id" class="text-gray-600 dark:text-gray-400 text-xs block">
                                        ID: {{ idCard.student.student_id }}
                                    </span>
                                    <span v-else-if="idCard.card_type === 'teacher' && idCard.teacher?.employee_id" class="text-gray-600 dark:text-gray-400 text-xs block">
                                        Employee ID: {{ idCard.teacher.employee_id }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ idCard.issue_date ? new Date(idCard.issue_date).toLocaleDateString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ idCard.expiry_date ? new Date(idCard.expiry_date).toLocaleDateString() : 'No expiry' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    idCard.status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                    idCard.status === 'expired' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                    'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="
                                        idCard.status === 'active' ? 'fas fa-check-circle' :
                                        idCard.status === 'expired' ? 'fas fa-exclamation-circle' :
                                        'fas fa-ban'
                                    "></i>
                                    {{ idCard.status ? idCard.status.charAt(0).toUpperCase() + idCard.status.slice(1) : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.id-cards.show', idCard.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.id-cards.edit', idCard.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button
                                        @click="deleteIdCard(idCard.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!idCards?.data || idCards.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-id-card text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No ID cards found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="idCards?.links" :links="idCards.links"/>
        </AdminPageWrapper>
    </App>
</template>

