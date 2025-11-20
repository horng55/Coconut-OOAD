<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Fees", href: route("admin.fees.index")}];

const props = defineProps({
    fees: Object,
    classes: Array,
    filters: Object,
    defaultAcademicYear: String,
});

const searchQuery = ref(props.filters?.search || "");
const selectedFeeType = ref(props.filters?.fee_type || "");
const selectedClass = ref(props.filters?.class_id || "");
const selectedAcademicYear = ref(props.filters?.academic_year || props.defaultAcademicYear || "");
const selectedStatus = ref(props.filters?.status || "");

const handleSearch = () => {
    router.get(route('admin.fees.index'), {
        search: searchQuery.value,
        fee_type: selectedFeeType.value,
        class_id: selectedClass.value,
        academic_year: selectedAcademicYear.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteFee = (id) => {
    if (confirm('Are you sure you want to delete this fee?')) {
        router.delete(route('admin.fees.destroy', id));
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount || 0);
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Fees Management"
            :subtitle="`Total: ${fees?.total || 0} fees`"
            icon="fas fa-money-bill-wave"
            icon-color="text-yellow-500"
            icon-bg="from-yellow-500/20 to-amber-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search fees..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                    />
                    <select
                        v-model="selectedFeeType"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                    >
                        <option value="">All Types</option>
                        <option value="tuition">Tuition</option>
                        <option value="library">Library</option>
                        <option value="sports">Sports</option>
                        <option value="exam">Exam</option>
                        <option value="transport">Transport</option>
                        <option value="hostel">Hostel</option>
                        <option value="other">Other</option>
                    </select>
                    <select
                        v-model="selectedClass"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                    >
                        <option value="">All Classes</option>
                        <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                            {{ classItem.name }}
                        </option>
                    </select>
                    <input
                        v-model="selectedAcademicYear"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Academic Year"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                    />
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <Link
                        :href="route('admin.fees.create')"
                        class="bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Add Fee
                    </Link>
                    <Link
                        :href="route('admin.fee-payments.index')"
                        class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-credit-card"></i>
                        Payments
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-yellow-500/10 to-amber-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Academic Year</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Due Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Payments</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(fee, index) in fees?.data || []" 
                            :key="fee.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-yellow-500/10 dark:bg-yellow-500/30 rounded-lg text-yellow-500 dark:text-yellow-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ fee.name || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium capitalize">
                                    {{ fee.fee_type || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ formatCurrency(fee.amount) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ fee.class_model?.name || 'All Classes' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ fee.academic_year || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ fee.due_date ? new Date(fee.due_date).toLocaleDateString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ fee.payments_count || 0 }} payment(s)
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    fee.status === 'active' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                ]">
                                    <i :class="fee.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                    {{ fee.status || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.fees.show', fee.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.fees.edit', fee.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button
                                        @click="deleteFee(fee.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!fees?.data || fees.data.length === 0">
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-money-bill-wave text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No fees found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="fees?.links" :links="fees.links"/>
        </AdminPageWrapper>
    </App>
</template>

