<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Fee Payments", href: route("admin.fee-payments.index")}];

const props = defineProps({
    payments: Object,
    fees: Array,
    students: Array,
    stats: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedStudent = ref(props.filters?.student_id || "");
const selectedFee = ref(props.filters?.fee_id || "");
const selectedStatus = ref(props.filters?.status || "");
const academicYear = ref(props.filters?.academic_year || "");
const perPage = ref(props.filters?.per_page || 10);

const handleSearch = () => {
    router.get(route('admin.fee-payments.index'), {
        search: searchQuery.value,
        student_id: selectedStudent.value,
        fee_id: selectedFee.value,
        status: selectedStatus.value,
        academic_year: academicYear.value,
        per_page: perPage.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
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
            title="Fee Payments"
            :subtitle="`Showing ${payments?.data?.length || 0} of ${payments?.total || 0} payments`"
            icon="fas fa-credit-card"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search payments..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    />
                    <select
                        v-model="selectedStudent"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    >
                        <option value="">All Students</option>
                        <option v-for="student in students" :key="student.id" :value="student.id">
                            {{ student.name }}
                        </option>
                    </select>
                    <select
                        v-model="selectedFee"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    >
                        <option value="">All Fees</option>
                        <option v-for="fee in fees" :key="fee.id" :value="fee.id">
                            {{ fee.name }}
                        </option>
                    </select>
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                        <option value="overdue">Overdue</option>
                        <option value="waived">Waived</option>
                    </select>
                    <input
                        v-model="academicYear"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Academic Year"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    />
                    <select
                        v-model="perPage"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    >
                        <option value="5">5 per page</option>
                        <option value="10">10 per page</option>
                        <option value="15">15 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </template>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm mb-1">Total Payments</p>
                            <p class="text-3xl font-bold">{{ stats?.total_payments || 0 }}</p>
                        </div>
                        <i class="fas fa-receipt text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Paid</p>
                            <p class="text-3xl font-bold">{{ stats?.paid_count || 0 }}</p>
                        </div>
                        <i class="fas fa-check-circle text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-amber-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm mb-1">Collected</p>
                            <p class="text-3xl font-bold">{{ formatCurrency(stats?.total_collected || 0) }}</p>
                        </div>
                        <i class="fas fa-dollar-sign text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-pink-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm mb-1">Pending</p>
                            <p class="text-3xl font-bold">{{ formatCurrency(stats?.total_pending || 0) }}</p>
                        </div>
                        <i class="fas fa-clock text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Fee</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Paid</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Remaining</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(payment, index) in payments?.data || []" 
                            :key="payment.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-green-500/10 dark:bg-green-500/30 rounded-lg text-green-500 dark:text-green-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ payment.student?.user?.full_name || 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ payment.student?.student_id || 'N/A' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800 dark:text-gray-200 font-medium">{{ payment.fee?.name || 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ payment.fee?.fee_type || 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">{{ formatCurrency(payment.amount) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(payment.amount_paid) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold" :class="payment.remaining_amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                    {{ formatCurrency(payment.remaining_amount) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    payment.status === 'paid' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : payment.status === 'partial'
                                        ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
                                        : payment.status === 'overdue'
                                        ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                        : payment.status === 'waived'
                                        ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="[
                                        payment.status === 'paid' ? 'fas fa-check-circle' :
                                        payment.status === 'partial' ? 'fas fa-exclamation-circle' :
                                        payment.status === 'overdue' ? 'fas fa-times-circle' :
                                        payment.status === 'waived' ? 'fas fa-hand-holding-heart' :
                                        'fas fa-clock'
                                    ]"></i>
                                    {{ payment.status || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        v-if="payment.status !== 'paid' && payment.fee"
                                        :href="route('admin.fees.payments.record', [payment.fee.id, payment.id])"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Record Payment"
                                    >
                                        <i class="fas fa-money-bill"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.fee-payments.show', payment.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        v-if="payment.fee"
                                        :href="route('admin.fees.show', payment.fee.id)"
                                        class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View Fee"
                                    >
                                        <i class="fas fa-money-bill-wave"></i>
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!payments?.data || payments.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-credit-card text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No payments found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="payments?.links" :links="payments.links"/>
        </AdminPageWrapper>
    </App>
</template>

